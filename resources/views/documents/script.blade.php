
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initTooltips();
        checkSyncStatus(); 

        // =========================================================
        // 1. MESIN UPLOAD KEPINGAN (NATIVE JS CHUNKING PRODUCTION)
        // =========================================================
        let uploadForm = document.getElementById('uploadForm');
        let btnUpload = document.getElementById('btnUploadChunk');
        
        if (uploadForm && btnUpload) {
            btnUpload.addEventListener('click', function (e) {
                e.preventDefault(); 

                let fileInput = document.querySelector('input[name="file"]');
                let titleInput = document.querySelector('input[name="title"]');
                let folderInput = document.getElementById('selectedFolderId');

                // Validasi Cepat
                if (!fileInput.files || fileInput.files.length === 0) {
                    alert("Pilih file terlebih dahulu!");
                    return;
                }
                if (!folderInput.value) {
                    alert("Mohon cari dan pilih Folder Tujuan dari dropdown!");
                    return;
                }

                let file = fileInput.files[0];
                let submitBtn = document.getElementById('btnUploadChunk');

                // Sembunyikan Modal Upload
                let uploadModalEl = document.getElementById('uploadModal');
                if (uploadModalEl) {
                    let uploadModal = bootstrap.Modal.getInstance(uploadModalEl);
                    if (uploadModal) uploadModal.hide();
                }

                // Tampilkan Modal Progress Bar
                let progressContainer = document.getElementById('progressOverlay');
                let progressBar = document.getElementById('uploadProgressBar');
                let progressText = document.getElementById('uploadProgressText');
                let statusMessage = document.getElementById('uploadStatusMessage');
                
                if (progressContainer) {
                    progressContainer.classList.remove('d-none');
                    progressContainer.classList.add('d-flex');
                }
                if (submitBtn) submitBtn.disabled = true;

                // --- MESIN PEMOTONG & IDENTITAS FILE ---
                const chunkSize = 2000000; // Potong per 2MB
                const totalChunks = Math.ceil(file.size / chunkSize);
                let currentChunk = 0;
                
                // UUID MUTLAK AGAR LARAVEL TIDAK MENCETAK FILE BERKALI-KALI
                const fileUuid = Date.now().toString(36) + '-' + file.name.replace(/[^a-zA-Z0-9]/g, '');

                function uploadNextChunk() {
                    let start = currentChunk * chunkSize;
                    let end = Math.min(start + chunkSize, file.size);
                    let chunk = file.slice(start, end);

                    let formData = new FormData();
                    formData.append('file', chunk, file.name);
                    formData.append('title', titleInput ? titleInput.value : file.name);
                    formData.append('folder_id', folderInput.value);
                    
                    let isPublicVal = document.getElementById('isPublicSelect') ? document.getElementById('isPublicSelect').value : '0';
                    formData.append('is_public', isPublicVal);

                    // PARAMETER WAJIB UNTUK LARAVEL PION CHUNK UPLOAD
                    formData.append('dzuuid', fileUuid); 
                    formData.append('dzchunkindex', currentChunk);
                    formData.append('dztotalfilesize', file.size);
                    formData.append('dzchunksize', chunkSize);
                    formData.append('dztotalchunkcount', totalChunks);
                    formData.append('dzchunkbyteoffset', start);

                    let xhr = new XMLHttpRequest();
                    xhr.open('POST', "{{ route('docs.chunk') }}", true); 
                    
                    let csrfToken = document.querySelector('input[name="_token"]').value;
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

                    xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable && progressBar && progressText) {
                            let loaded = start + e.loaded;
                            let percentComplete = Math.round((loaded / file.size) * 100);
                            
                            if(percentComplete > 100) percentComplete = 100;

                            progressBar.style.width = percentComplete + '%';
                            progressBar.setAttribute('aria-valuenow', percentComplete);
                            progressText.innerText = percentComplete + '%';
                        }
                    });

                    xhr.onload = function () {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            currentChunk++;
                            if (currentChunk < totalChunks) {
                                // Looping: Lanjut kirim kepingan berikutnya
                                uploadNextChunk(); 
                            } else {
                                // SELESAI 100%, LARAVEL BARU AKAN INSERT KE DATABASE
                                if (progressBar) {
                                    progressBar.classList.remove('bg-primary');
                                    progressBar.classList.add('bg-success');
                                }
                                if (statusMessage) {
                                    statusMessage.innerHTML = "<span class='text-success fw-bold'><i class='bi bi-check-circle-fill'></i> Selesai! Memproses ke Cloud...</span>";
                                }
                                setTimeout(() => { window.location.reload(); }, 1000);
                            }
                        } else {
                            alert("Terjadi kesalahan sistem saat mengirim potongan file ke-" + (currentChunk + 1));
                            resetUI();
                        }
                    };

                    xhr.onerror = function () {
                        alert("Koneksi terputus! Gagal mengunggah potongan file.");
                        resetUI();
                    };

                    xhr.send(formData);
                }

                function resetUI() {
                    if (progressContainer) {
                        progressContainer.classList.remove('d-flex');
                        progressContainer.classList.add('d-none');
                    }
                    if (submitBtn) submitBtn.disabled = false;
                }

                uploadNextChunk();
            });
        }

        // =========================================================
        // 2. LOGIKA CUSTOM SEARCHABLE DROPDOWN FOLDER (UPLOAD)
        // =========================================================
        const folderDropdownBtn = document.getElementById('folderDropdownBtn');
        const folderSearchInput = document.getElementById('folderSearchInput');
        const folderOptions = document.querySelectorAll('.folder-option');
        const noFolderFound = document.getElementById('noFolderFound');
        const selectedFolderId = document.getElementById('selectedFolderId');
        const folderDropdownText = document.getElementById('folderDropdownText');

        if (folderDropdownBtn && folderSearchInput) {
            folderDropdownBtn.addEventListener('shown.bs.dropdown', function () {
                folderSearchInput.focus();
            });

            folderSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                let visibleCount = 0;

                folderOptions.forEach(option => {
                    const text = option.innerText.toLowerCase();
                    if (text.includes(searchTerm)) {
                        option.style.setProperty('display', 'flex', 'important');
                        visibleCount++;
                    } else {
                        option.style.setProperty('display', 'none', 'important');
                    }
                });

                if (noFolderFound) {
                    if (visibleCount === 0) {
                        noFolderFound.classList.remove('d-none');
                    } else {
                        noFolderFound.classList.add('d-none');
                    }
                }
            });

            folderOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const folderId = this.getAttribute('data-id');
                    const folderText = this.innerText;

                    if (selectedFolderId) selectedFolderId.value = folderId;
                    if (folderDropdownText) {
                        folderDropdownText.innerText = folderText;
                        folderDropdownText.classList.remove('text-muted');
                        folderDropdownText.classList.add('text-dark', 'fw-bold');
                    }

                    folderSearchInput.value = '';
                    folderSearchInput.dispatchEvent(new Event('input')); 
                });
            });
        }

        // =========================================================
        // 3. LOGIKA DROPDOWN FILTER PENCARIAN (HALAMAN UTAMA)
        // =========================================================
        const filterFolderBtn = document.getElementById('filterFolderBtn');
        const filterFolderSearch = document.getElementById('filterFolderSearch');
        const filterFolderOpts = document.querySelectorAll('.filter-folder-opt');
        const filterNoFolderFound = document.getElementById('filterNoFolderFound');
        const filterFolderId = document.getElementById('filterFolderId');
        const filterFolderText = document.getElementById('filterFolderText');

        if(filterFolderBtn && filterFolderSearch) {
            filterFolderBtn.addEventListener('shown.bs.dropdown', () => filterFolderSearch.focus());

            filterFolderSearch.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                let visible = 0;
                
                filterFolderOpts.forEach(opt => {
                    const text = opt.innerText.toLowerCase();
                    if(text.includes(term)) {
                        opt.style.setProperty('display', 'flex', 'important');
                        visible++;
                    } else {
                        opt.style.setProperty('display', 'none', 'important');
                    }
                });
                
                if(filterNoFolderFound) {
                    if(visible === 0) {
                        filterNoFolderFound.classList.remove('d-none');
                    } else {
                        filterNoFolderFound.classList.add('d-none');
                    }
                }
            });

            filterFolderOpts.forEach(opt => {
                opt.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const text = this.getAttribute('data-text');

                    if (filterFolderId) filterFolderId.value = id;
                    if (filterFolderText) filterFolderText.innerText = text;

                    filterFolderOpts.forEach(o => o.classList.remove('bg-primary', 'bg-opacity-10', 'text-primary', 'fw-bold'));
                    this.classList.add('bg-primary', 'bg-opacity-10', 'text-primary', 'fw-bold');
                });
            });
        }
    });

    // =========================================================
    // 4. MESIN PENCARI LIVE SEARCH UNTUK MODAL SHARE
    // =========================================================
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('search-user-input') || e.target.classList.contains('search-dept-input')) {
            let targetId = e.target.getAttribute('data-target');
            let term = e.target.value.toLowerCase();
            let items = document.querySelectorAll('#' + targetId + ' a');
            
            items.forEach(item => {
                if (item.innerText.toLowerCase().includes(term)) {
                    item.style.setProperty('display', 'block', 'important');
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });
        }
    });

    // =========================================================
    // 5. FUNGSI UTILITAS UMUM (100% AMAN)
    // =========================================================
    function selectShareOption(type, docId, id, text) {
        let hiddenInputId = (type === 'user' ? 'selectedUser_' : 'selectedDept_') + docId;
        let textSpanId = (type === 'user' ? 'userText_' : 'deptText_') + docId;
        
        let hiddenInput = document.getElementById(hiddenInputId);
        if(hiddenInput) hiddenInput.value = id;
        
        let span = document.getElementById(textSpanId);
        if(span) {
            span.innerText = text;
            span.classList.remove('text-muted');
            span.classList.add('text-dark', 'fw-bold');
        }
    }

    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    function toggleShareUI(val, id) {
        let userField = document.getElementById('user_field_' + id);
        let deptField = document.getElementById('dept_field_' + id);
        let accessField = document.getElementById('access_level_field_' + id);

        if(val === 'user') {
            if(userField) userField.style.display = 'block';
            if(deptField) deptField.style.display = 'none';
            if(accessField) accessField.style.display = 'block';
        } else if (val === 'department') {
            if(userField) userField.style.display = 'none';
            if(deptField) deptField.style.display = 'block';
            if(accessField) accessField.style.display = 'block';
        } else if (val === 'public') {
            if(userField) userField.style.display = 'none';
            if(deptField) deptField.style.display = 'none';
            if(accessField) accessField.style.display = 'block';
        }
    }

    function checkSyncStatus() {
        let syncingBadges = document.querySelectorAll('.syncing-indicator');
        if(syncingBadges.length > 0) {
            setTimeout(() => {
                fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    
                    let newTable = doc.querySelector('.table-responsive');
                    let currentTable = document.querySelector('.table-responsive');
                    
                    if (newTable && currentTable) {
                        currentTable.innerHTML = newTable.innerHTML;
                        initTooltips(); 
                        checkSyncStatus(); 
                    }
                }).catch(e => console.error(e));
            }, 5000); 
        }
    }
</script>

    <style>
        .input-group-custom { position: relative; }
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); z-index: 10; color: #5f6368; }
        .md-input { padding-left: 40px !important; border-radius: 10px; border: 1px solid #dadce0; }
        .md-file-input { border-radius: 10px; border: 2px dashed #dadce0; padding: 10px; background: #f8f9fa; }
        .file-icon-box { width: 48px; height: 48px; }
        
        .pagination { margin-bottom: 0; }
        .page-item.active .page-link { background-color: #212529; border-color: #212529; }
        .page-link { color: #212529; padding: 0.5rem 1rem; border-radius: 8px; margin: 0 3px; border: 1px solid #dee2e6; }
        .page-item:not(.active) .page-link:hover { background-color: #f8f9fa; color: #000; }
        .page-item.disabled .page-link { background-color: transparent; }
    </style>
    