@extends('layouts.master')
@section('title', 'Create Notification')

@section('pageStyle')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        .card-body {
            overflow: hidden;
        }

        .border-dashed {
            border-style: dashed !important;
        }

        .file-icon {
            font-size: 2.5rem;
            line-height: 1;
        }

        .file-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .form-container {
            max-width: 900px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .form-container {
                max-width: 100%;
            }
        }

        .drag-active {
            border-color: #007bff !important;
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('content')
    <!-- Content -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/</span> Create Notification</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4 form-container">
                    <h5 class="card-header">
                        <div class="d-flex justify-content-between">
                            <span class="text-center">Form Create Notification</span>
                            <span class="text-center">
                                <a href="{{ route('notifications.list') }}" class="btn btn-secondary waves-effect">
                                    <i class="mdi mdi-arrow-left"></i> Kembali
                                </a>
                            </span>
                        </div>
                    </h5>
                    <div class="card-body">
                        <form id="notificationForm" action="{{ route('notifications.save') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Jenis -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Jenis Notifikasi</label>
                                <div class="col-sm-10">
                                    <select name="jenis" class="form-select" required>
                                        <option value="Email">Email</option>
                                    </select>
                                </div>
                            </div>

                            <!-- To -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">To</label>
                                <div class="col-sm-10">
                                    <input type="email" name="to" class="form-control" placeholder="Email tujuan" required>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <input type="text" name="title" class="form-control" placeholder="Judul notifikasi" required>
                                </div>
                            </div>

                            <!-- Body -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Body</label>
                                <div class="col-sm-10">
                                    <textarea name="body" class="form-control" rows="4" placeholder="Isi notifikasi" required></textarea>
                                </div>
                            </div>

                            <!-- Kirim Pada -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Kirim Pada</label>
                                <div class="col-sm-10">
                                    <input type="datetime-local" name="kirim_pada" class="form-control" required>
                                </div>
                            </div>

                            <!-- Lampiran (Enhanced Drag & Drop) -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label fw-bold">Lampiran</label>
                                <div class="col-sm-10">
                                    <div class="card border-dashed border-2" id="dropzone-card">
                                        <div class="card-body text-center py-5" id="dropzone-area">
                                            <div id="drop-area">
                                                <i class="mdi mdi-cloud-upload-outline text-muted mb-3" style="font-size: 48px;"></i>
                                                <h5 class="text-muted mb-2">Drop files here or click to browse</h5>
                                                <p class="text-muted small mb-3">Maximum 3 files • PDF, DOC, DOCX, JPG, PNG (Max 10MB each)</p>
                                                <button type="button" class="btn btn-outline-primary" id="browse-files">
                                                    <i class="mdi mdi-folder-open-outline me-2"></i>Browse Files
                                                </button>
                                                <input type="file" id="file-input" name="files[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display: none;" max="3">
                                            </div>
                                            <div id="file-preview" class="mt-4" style="display: none;">
                                                <div class="row" id="file-list">
                                                    <!-- Files will be displayed here -->
                                                </div>
                                            </div>
                                            <div id="upload-progress" style="display: none;">
                                                <div class="progress mt-3">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                                                </div>
                                                <p class="text-muted mt-2">Uploading...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    <button type="reset" class="btn btn-label-secondary">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pageScript')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropzoneArea = document.getElementById('dropzone-area');
            const fileInput = document.getElementById('file-input');
            const browseButton = document.getElementById('browse-files');
            const filePreview = document.getElementById('file-preview');
            const fileList = document.getElementById('file-list');
            const uploadProgress = document.getElementById('upload-progress');
            let selectedFiles = [];
            const MAX_FILES = 3;
            const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB in bytes

            // Event Listeners
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzoneArea.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzoneArea.addEventListener(eventName, () => dropzoneArea.classList.add('drag-active'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzoneArea.addEventListener(eventName, () => dropzoneArea.classList.remove('drag-active'), false);
            });

            dropzoneArea.addEventListener('drop', handleDrop, false);
            browseButton.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', handleFiles, false);

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles({ target: { files: files } });
            }

            function handleFiles(event) {
                const files = event.target.files;
                if (!files || files.length === 0) return;

                // Check file limit
                if (selectedFiles.length + files.length > MAX_FILES) {
                    Swal.fire({
                        title: 'Warning!',
                        text: `You can only upload a maximum of ${MAX_FILES} files.`,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Validate file size and add files
                for (let i = 0; i < files.length; i++) {
                    if (files[i].size > MAX_FILE_SIZE) {
                        Swal.fire({
                            title: 'File Too Large!',
                            text: `File "${files[i].name}" exceeds the maximum size of 10MB.`,
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                        continue;
                    }
                    selectedFiles.push(files[i]);
                }

                updateFilePreview();
                updateFormData();
            }

            function updateFilePreview() {
                fileList.innerHTML = '';
                if (selectedFiles.length > 0) {
                    filePreview.style.display = 'block';
                    selectedFiles.forEach((file, index) => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'col-lg-4 col-md-6 mb-3';
                        fileItem.innerHTML = `
                            <div class="d-flex align-items-center bg-light p-3 rounded">
                                <i class="${getFileIcon(file.type)} text-primary file-icon me-3"></i>
                                <div class="flex-grow-1">
                                    <div class="file-name fw-bold small">${file.name}</div>
                                    <small class="text-muted">${formatFileSize(file.size)}</small>
                                </div>
                                <button type="button" class="btn btn-icon btn-sm btn-outline-danger ms-2" onclick="removeFile(${index})">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        `;
                        fileList.appendChild(fileItem);
                    });
                } else {
                    filePreview.style.display = 'none';
                }
            }

            function updateFormData() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                fileInput.files = dataTransfer.files;
            }

            function getFileIcon(mimeType) {
                switch (mimeType) {
                    case 'application/pdf':
                        return 'mdi mdi-file-pdf-box';
                    case 'application/msword':
                    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                        return 'mdi mdi-file-word-box';
                    case 'image/jpeg':
                    case 'image/jpg':
                    case 'image/png':
                        return 'mdi mdi-file-image-box';
                    default:
                        return 'mdi mdi-file-document-box';
                }
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Global function to remove file
            window.removeFile = function (index) {
                selectedFiles.splice(index, 1);
                updateFilePreview();
                updateFormData();
            };

            // Form submission handler
            $('#notificationForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission
                
                let formData = new FormData(this);
                
                // Show progress bar
                $('#upload-progress').show();
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                $('.progress-bar').css('width', percentComplete + '%')
                                    .attr('aria-valuenow', percentComplete)
                                    .text(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        $('#upload-progress').hide();
                        Swal.fire({
                            title: 'Success!',
                            text: 'Notification has been saved and sent successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('notifications.list') }}";
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        $('#upload-progress').hide();
                        let errorMessage = 'An error occurred while saving the notification.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
@endsection