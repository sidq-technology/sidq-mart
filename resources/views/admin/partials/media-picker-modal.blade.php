<!-- ==============================================================
     SIDQ Enterprise Media Library Picker Modal
     Supports: Gallery selection, Primary image selection, URL upload
=============================================================== -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-labelledby="mediaPickerModalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(37, 99, 235, 0.1); color: #2563eb; font-size: 16px;">
                        <i class="fas fa-photo-film"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0 fs-6" id="mediaPickerModalLabel">মিডিয়া লাইব্রেরি (Media Library)</h6>
                        <span class="text-muted small" style="font-size: 11.5px;">পূর্বে আপলোড করা ছবি নির্বাচন করুন অথবা নতুন যুক্ত করুন</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Nav Tabs & Controls Bar -->
            <div class="px-4 pt-3 pb-2 bg-light border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                <ul class="nav nav-pills gap-1" id="mediaPickerTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active py-1 px-3 fw-semibold small rounded-pill" id="tabMediaBrowseBtn" data-bs-toggle="pill" data-bs-target="#tabMediaBrowse" type="button">
                            <i class="fas fa-images me-1"></i> মিডিয়া ব্রাউজ
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-1 px-3 fw-semibold small rounded-pill" id="tabMediaUploadBtn" data-bs-toggle="pill" data-bs-target="#tabMediaUpload" type="button">
                            <i class="fas fa-cloud-arrow-up me-1"></i> নতুন আপলোড / URL
                        </button>
                    </li>
                </ul>

                <!-- Filter Controls (for Browse tab) -->
                <div class="d-flex align-items-center gap-2" id="mediaFilterBar">
                    <select id="mediaFolderFilter" class="form-select form-select-sm py-1" style="width: 140px; font-size: 12px;">
                        <option value="all">সকল ফোল্ডার</option>
                        <option value="products">Products</option>
                        <option value="banners">Banners</option>
                        <option value="categories">Categories</option>
                    </select>
                    <div class="input-group input-group-sm" style="width: 190px;">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="mediaSearchInput" class="form-control" placeholder="নাম খুঁজুন..." style="font-size: 12px;">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" id="btnRefreshMedia" title="রিফ্রেশ">
                        <i class="fas fa-rotate"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <div class="tab-content">
                    <!-- Tab 1: Browse Existing Media -->
                    <div class="tab-pane fade show active" id="tabMediaBrowse" role="tabpanel">
                        <!-- Loading State -->
                        <div id="mediaLoader" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                            <div class="small text-muted mt-2">মিডিয়া লোড হচ্ছে...</div>
                        </div>

                        <!-- Empty State -->
                        <div id="mediaEmptyState" class="text-center py-5 d-none">
                            <i class="fas fa-folder-open text-muted fs-1 mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">কোনো ছবি পাওয়া যায়নি</h6>
                            <p class="text-muted small">নতুন ছবি আপলোড করতে পাশের 'নতুন আপলোড / URL' ট্যাবে যান।</p>
                        </div>

                        <!-- Media Grid -->
                        <div id="mediaGrid" class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- Tab 2: Upload New / From URL -->
                    <div class="tab-pane fade" id="tabMediaUpload" role="tabpanel">
                        <div class="row g-4">
                            <!-- Section A: File Upload -->
                            <div class="col-12 col-md-6">
                                <div class="p-4 border rounded-3 bg-light text-center h-100 d-flex flex-column justify-content-center">
                                    <div class="mb-3">
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(37,99,235,0.1); color: #2563eb; font-size: 22px;">
                                            <i class="fas fa-upload"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">ডিভাইস থেকে ছবি আপলোড</h6>
                                    <p class="text-muted small mb-3">JPG, PNG, WebP বা GIF (সর্বোচ্চ ৫ MB)</p>
                                    <input type="file" id="mediaDirectFileInput" class="form-control mb-2" accept="image/*">
                                    <button type="button" class="btn btn-primary fw-bold" id="btnUploadDirectFile">
                                        <i class="fas fa-cloud-arrow-up me-1"></i> আপলোড করুন
                                    </button>
                                </div>
                            </div>

                            <!-- Section B: From URL -->
                            <div class="col-12 col-md-6">
                                <div class="p-4 border rounded-3 bg-light text-center h-100 d-flex flex-column justify-content-center">
                                    <div class="mb-3">
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(16,185,129,0.1); color: #10b981; font-size: 22px;">
                                            <i class="fas fa-link"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">ইমেজ ইউআরএল (Image URL) থেকে আপলোড</h6>
                                    <p class="text-muted small mb-3">অনলাইনের যেকোনো ইমেজের লিংক দিলে সরাসরি সার্ভারে ডাউনলোড ও যুক্ত হবে</p>
                                    <input type="url" id="mediaDirectUrlInput" class="form-control mb-2" placeholder="https://example.com/product-image.jpg">
                                    <button type="button" class="btn btn-success fw-bold" id="btnUploadDirectUrl">
                                        <i class="fas fa-download me-1"></i> ডাউনলোড ও সংরক্ষণ
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Feedback Box -->
                        <div id="mediaUploadFeedback" class="alert d-none mt-3 py-2 px-3 small"></div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white py-2 px-4 border-top d-flex align-items-center justify-content-between">
                <div class="small text-muted" id="mediaSelectedCounter">
                    <span>কোনো ছবি সিলেক্ট করা নেই</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">বাতিল</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold" id="btnConfirmMediaSelection" disabled>
                        <i class="fas fa-check me-1"></i> নির্বাচিত ছবি যুক্ত করুন
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.media-card {
    cursor: pointer;
    border: 2px solid #e2e8f0;
    transition: all 0.18s ease;
    border-radius: 10px;
    background: #fff;
    position: relative;
    overflow: hidden;
}
.media-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    transform: translateY(-2px);
}
.media-card.selected {
    border-color: #2563eb !important;
    background: rgba(37, 99, 235, 0.04);
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.35);
}
.media-card .select-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 22px;
    height: 22px;
    background: #2563eb;
    color: #fff;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.media-card.selected .select-badge {
    display: flex;
}
.media-thumb-img {
    width: 100%;
    height: 115px;
    object-fit: cover;
    background: #f8fafc;
}
.media-info-box {
    padding: 6px 8px;
    font-size: 11px;
    line-height: 1.25;
}
</style>
@endpush

@push('scripts')
<script>
window.SIDQMediaPicker = (function () {
    let currentTarget = 'primary'; // 'primary' or 'gallery'
    let isMultiple = false;
    let selectedItems = []; // Array of { path, url, name }
    let allMediaItems = [];

    const modalEl = document.getElementById('mediaPickerModal');
    let bsModalInstance = null;

    function getModal() {
        if (!bsModalInstance && modalEl) {
            bsModalInstance = new bootstrap.Modal(modalEl);
        }
        return bsModalInstance;
    }

    function open(targetType = 'primary', multiple = false) {
        currentTarget = targetType;
        isMultiple = multiple;
        selectedItems = [];
        updateFooterSelection();

        const titleEl = document.getElementById('mediaPickerModalLabel');
        if (titleEl) {
            titleEl.textContent = currentTarget === 'primary' 
                ? 'প্রধান ছবির জন্য মিডিয়া সিলেক্ট করুন' 
                : 'গ্যালারির জন্য মিডিয়া সিলেক্ট করুন';
        }

        getModal()?.show();
        loadMedia();
    }

    function loadMedia() {
        const loader = document.getElementById('mediaLoader');
        const emptyState = document.getElementById('mediaEmptyState');
        const grid = document.getElementById('mediaGrid');
        const folder = document.getElementById('mediaFolderFilter')?.value || 'all';
        const search = document.getElementById('mediaSearchInput')?.value || '';

        if (loader) loader.classList.remove('d-none');
        if (emptyState) emptyState.classList.add('d-none');
        if (grid) grid.innerHTML = '';

        const params = new URLSearchParams();
        if (folder !== 'all') params.append('folder', folder);
        if (search.trim()) params.append('search', search.trim());

        fetch('{{ route('admin.media.index') }}?' + params.toString())
            .then(res => res.json())
            .then(data => {
                if (loader) loader.classList.add('d-none');
                if (data.success && data.items && data.items.length > 0) {
                    allMediaItems = data.items;
                    renderMediaGrid(data.items);
                } else {
                    if (emptyState) emptyState.classList.remove('d-none');
                }
            })
            .catch(err => {
                if (loader) loader.classList.add('d-none');
                console.error('Media load error:', err);
                if (emptyState) emptyState.classList.remove('d-none');
            });
    }

    function renderMediaGrid(items) {
        const grid = document.getElementById('mediaGrid');
        if (!grid) return;
        grid.innerHTML = '';

        items.forEach(item => {
            const col = document.createElement('div');
            col.className = 'col';

            const isSel = selectedItems.some(s => s.path === item.path);

            col.innerHTML = `
                <div class="media-card ${isSel ? 'selected' : ''}" data-path="${escapeHtml(item.path)}" data-url="${escapeHtml(item.url)}" data-name="${escapeHtml(item.name)}">
                    <div class="select-badge"><i class="fas fa-check"></i></div>
                    <img src="${escapeHtml(item.url)}" alt="${escapeHtml(item.name)}" class="media-thumb-img" loading="lazy" onerror="this.src='{{ asset('images/placeholder-product.png') }}'">
                    <div class="media-info-box border-top">
                        <div class="text-truncate fw-semibold text-dark" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</div>
                        <div class="text-muted d-flex justify-content-between mt-1" style="font-size: 10px;">
                            <span>${escapeHtml(item.folder)}</span>
                            <span>${escapeHtml(item.size)}</span>
                        </div>
                    </div>
                </div>
            `;

            col.querySelector('.media-card').addEventListener('click', function () {
                handleCardClick(this, item);
            });

            grid.appendChild(col);
        });
    }

    function handleCardClick(cardEl, item) {
        if (!isMultiple) {
            // Single select mode
            document.querySelectorAll('.media-card.selected').forEach(c => c.classList.remove('selected'));
            cardEl.classList.add('selected');
            selectedItems = [item];
        } else {
            // Multi select mode
            const index = selectedItems.findIndex(s => s.path === item.path);
            if (index > -1) {
                selectedItems.splice(index, 1);
                cardEl.classList.remove('selected');
            } else {
                selectedItems.push(item);
                cardEl.classList.add('selected');
            }
        }
        updateFooterSelection();
    }

    function updateFooterSelection() {
        const counter = document.getElementById('mediaSelectedCounter');
        const confirmBtn = document.getElementById('btnConfirmMediaSelection');

        if (counter) {
            if (selectedItems.length === 0) {
                counter.innerHTML = '<span>কোনো ছবি সিলেক্ট করা নেই</span>';
            } else {
                counter.innerHTML = `<span class="text-primary fw-bold">${selectedItems.length} টি ছবি সিলেক্টেড</span>`;
            }
        }

        if (confirmBtn) {
            confirmBtn.disabled = (selectedItems.length === 0);
        }
    }

    function confirmSelection() {
        if (selectedItems.length === 0) return;

        if (currentTarget === 'primary') {
            const item = selectedItems[0];
            const mediaInput = document.getElementById('primaryImageMediaInput');
            const previewBox = document.getElementById('primaryImagePreviewBox');
            const previewImg = document.getElementById('primaryImagePreviewImg');
            const previewName = document.getElementById('primaryImagePreviewName');

            if (mediaInput) mediaInput.value = item.path;
            if (previewImg) previewImg.src = item.url;
            if (previewName) previewName.textContent = item.name;
            if (previewBox) previewBox.classList.remove('d-none');

            // Clear URL or file input if media selected
            const fileInput = document.getElementById('primary_image');
            const urlInput = document.getElementById('primary_image_url');
            if (fileInput) fileInput.value = '';
            if (urlInput) urlInput.value = '';
        } else if (currentTarget === 'gallery') {
            const container = document.getElementById('selectedGalleryMediaContainer');
            if (container) {
                selectedItems.forEach(item => {
                    // Check if already added
                    if (container.querySelector(`input[value="${escapeHtml(item.path)}"]`)) return;

                    const chip = document.createElement('div');
                    chip.className = 'position-relative border rounded p-1 bg-white shadow-xs gallery-media-chip';
                    chip.style.width = '75px';
                    chip.style.height = '75px';
                    chip.innerHTML = `
                        <img src="${escapeHtml(item.url)}" class="w-100 h-100 rounded" style="object-fit: cover;">
                        <input type="hidden" name="gallery_image_media[]" value="${escapeHtml(item.path)}">
                        <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 p-0 rounded-circle d-flex align-items-center justify-content-center" style="width: 18px; height: 18px; transform: translate(30%, -30%);" onclick="this.closest('.gallery-media-chip').remove()">
                            <i class="fas fa-times" style="font-size: 9px;"></i>
                        </button>
                    `;
                    container.appendChild(chip);
                });
            }
        }

        getModal()?.hide();
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Initialize DOM events
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('btnConfirmMediaSelection')?.addEventListener('click', confirmSelection);
        document.getElementById('mediaFolderFilter')?.addEventListener('change', loadMedia);
        document.getElementById('btnRefreshMedia')?.addEventListener('click', loadMedia);

        let searchDebounce = null;
        document.getElementById('mediaSearchInput')?.addEventListener('input', function () {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(loadMedia, 300);
        });

        // Upload Direct File
        document.getElementById('btnUploadDirectFile')?.addEventListener('click', function () {
            const fileInput = document.getElementById('mediaDirectFileInput');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                alert('অনুগ্রহ করে একটি ছবি নির্বাচন করুন।');
                return;
            }

            const fd = new FormData();
            fd.append('file', fileInput.files[0]);
            fd.append('folder', 'products');

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> আপলোড হচ্ছে...';

            fetch('{{ route('admin.media.upload') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (data.success) {
                    fileInput.value = '';
                    // Switch to browse tab and refresh
                    document.getElementById('tabMediaBrowseBtn')?.click();
                    loadMedia();
                } else {
                    alert(data.message || 'আপলোড ব্যর্থ হয়েছে।');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                alert('সার্ভার এরর: ' + err.message);
            });
        });

        // Upload from URL
        document.getElementById('btnUploadDirectUrl')?.addEventListener('click', function () {
            const urlInput = document.getElementById('mediaDirectUrlInput');
            const urlVal = urlInput ? urlInput.value.trim() : '';
            if (!urlVal) {
                alert('অনুগ্রহ করে একটি সঠিক ইমেজ ইউআরএল পেস্ট করুন।');
                return;
            }

            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> ডাউনলোড হচ্ছে...';

            fetch('{{ route('admin.media.upload') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ url: urlVal, folder: 'products' })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (data.success) {
                    urlInput.value = '';
                    document.getElementById('tabMediaBrowseBtn')?.click();
                    loadMedia();
                } else {
                    alert(data.message || 'ডাউনলোড ব্যর্থ হয়েছে।');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                alert('সার্ভার এরর: ' + err.message);
            });
        });
    });

    return {
        open: open
    };
})();
</script>
@endpush
