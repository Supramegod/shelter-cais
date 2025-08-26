@extends('layouts.master')
@section('title','SDT Training')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">SDT/ </span> SDT Training Baru</h4>
  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form SDT Training</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('sdt-training.save')}}" method="POST" id="training-form">
          @csrf
          
          <div class="row mb-4">
            <div class="col-12">
              <div class="progress" style="height: 6px;">
                <div class="progress-bar" role="progressbar" id="form-progress" style="width: 0%"></div>
              </div>
              <div class="mt-2">
                <small class="text-muted">Langkah <span id="current-step">1</span> dari 7</small>
              </div>
            </div>
          </div>

          <div class="form-step active" data-step="1">
            <h6 class="mb-3">Langkah 1: Pilih Business Unit & Area</h6>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Business Unit <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <div class="position-relative">
                  <select id="laman_id" name="laman_id" class="select2 form-select" data-allow-clear="true" tabindex="-1" required>
                    <option value="">- Pilih Business Unit -</option>
                    @foreach($listBu as $value)
                    <option value="{{$value->id}}" @if(old('laman_id') == $value->id) selected @endif>{{$value->laman}}</option>
                    @endforeach
                  </select>
                  <div class="invalid-feedback">Business Unit harus dipilih</div>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Area <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <div class="position-relative">
                  <select id="area_id" name="area_id" class="select2 form-select" data-allow-clear="true" tabindex="-1" required>
                    <option value="">- Pilih Business Unit dulu -</option>
                  </select>
                  <div class="invalid-feedback">Area harus dipilih</div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-step" data-step="2">
            <h6 class="mb-3">Langkah 2: Pilih Client</h6>
            
            <div class="alert alert-info" id="debug-info">
              <small>
                Business Unit: <span id="debug-bu">-</span><br>
                Area: <span id="debug-area">-</span>
              </small>
            </div>
            
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Client <span class="text-danger">*</span></label>
              <div class="col-sm-6">
                <div class="position-relative">
                  <select multiple id="client_id" name="client_id[]" class="select2 form-select" data-allow-clear="true" tabindex="-1" required>
                    <option value="">- Loading clients... -</option>
                  </select>
                  <div class="invalid-feedback">Pilih minimal satu client</div>
                  <small class="form-text text-muted">Gunakan Ctrl+Click untuk memilih multiple client</small>
                </div>
              </div>
            </div>
          </div>

          <div class="form-step" data-step="3">
            <h6 class="mb-3">Langkah 3: Pilih Trainer</h6>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Trainer <span class="text-danger">*</span></label>
              <div class="col-sm-6">
                <div class="position-relative">
                  <select id="trainer_id" multiple name="trainer_id[]" class="select2 form-select" data-allow-clear="true" tabindex="-1" required>
                    <option value="">- Pilih Trainer -</option>
                    @foreach($listTrainer as $value)
                    @if($value->id==99) @continue @endif
                    <option value="{{$value->id}}" @if(old('trainer_id') == $value->id) selected @endif>{{$value->trainer}}</option>
                    @endforeach
                  </select>
                  <div class="invalid-feedback">Minimal pilih satu trainer</div>
                  <small class="form-text text-muted">Gunakan Ctrl+Click untuk memilih multiple trainer</small>
                </div>
              </div>
            </div>
          </div>

          <div class="form-step" data-step="4">
            <h6 class="mb-3">Langkah 4: Pilih Materi Training</h6>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Materi <span class="text-danger">*</span></label>
              <div class="col-sm-6">
                <div class="position-relative">
                  <select id="materi_id" name="materi_id" class="select2 form-select" data-allow-clear="true" tabindex="-1" required>
                    <option value="">- Pilih Materi -</option>
                    @foreach($listMateri as $value)
                    <option value="{{$value->id}}" @if(old('materi_id') == $value->id) selected @endif>{{$value->nama}}</option>
                    @endforeach
                  </select>
                  <div class="invalid-feedback">Materi harus dipilih</div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-step" data-step="5">
            <h6 class="mb-3">Langkah 5: Tentukan Tempat Training</h6>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Tempat <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <div class="position-relative">
                  <select id="tempat_id" name="tempat_id" class="select2 form-select" data-allow-clear="true" tabindex="-1" required>
                      <option value="">- Pilih Tempat -</option>
                      <option value="1" @if(old('tempat_id') == '1') selected @endif>IN DOOR</option>
                      <option value="2" @if(old('tempat_id') == '2') selected @endif>OUT DOOR</option>
                  </select>
                  <div class="invalid-feedback">Tempat harus dipilih</div>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Alamat</label>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <textarea class="form-control h-px-100" name="alamat" id="alamat" placeholder="Masukkan alamat detail training">{{old('alamat')}}</textarea>
                  <label for="alamat">Alamat Detail</label>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Link Zoom</label>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <textarea class="form-control h-px-100" name="link_zoom" id="link_zoom" placeholder="Masukkan link zoom jika training online">{{old('link_zoom')}}</textarea>
                  <label for="link_zoom">Link Zoom (Opsional)</label>
                </div>
              </div>
            </div>
          </div>

          <div class="form-step" data-step="6">
            <h6 class="mb-3">Langkah 6: Tentukan Jadwal Training</h6>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Waktu Mulai <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <div class="position-relative">
                  <input type="datetime-local" id="start_date" name="start_date" value="{{old('start_date')}}" class="form-control" required>
                  <div class="invalid-feedback">Waktu mulai harus diisi</div>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Waktu Selesai <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <div class="position-relative">
                  <input type="datetime-local" id="end_date" name="end_date" value="{{old('end_date')}}" class="form-control" required>
                  <div class="invalid-feedback">Waktu selesai harus diisi</div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-step" data-step="7">
            <h6 class="mb-3">Langkah 7: Informasi Tambahan</h6>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Keterangan</label>
              <div class="col-sm-8">
                <div class="form-floating form-floating-outline">
                  <textarea class="form-control h-px-150" name="keterangan" id="keterangan" placeholder="Masukkan keterangan atau catatan tambahan">{{old('keterangan')}}</textarea>
                  <label for="keterangan">Keterangan Tambahan</label>
                </div>
              </div>
            </div>

            <div id="form-summary" class="mt-4">
              <div class="alert alert-info">
                <h6><i class="mdi mdi-information"></i> Ringkasan Training</h6>
                <div id="summary-content"></div>
              </div>
            </div>
          </div>

          <hr class="my-4 mx-4">
          <div class="pt-4">
            <div class="row justify-content-between">
              <div class="col-sm-6">
                <button type="button" id="prev-btn" class="btn btn-outline-secondary waves-effect" style="display: none;">
                  <i class="mdi mdi-arrow-left"></i> Sebelumnya
                </button>
              </div>
              <div class="col-sm-6 d-flex justify-content-end">
                <button type="button" id="next-btn" class="btn btn-primary waves-effect waves-light me-2">
                  Selanjutnya <i class="mdi mdi-arrow-right"></i>
                </button>
                <button type="submit" id="submit-btn" class="btn btn-success waves-effect waves-light me-2 hidden-button">
                  <i class="mdi mdi-check"></i> Simpan Training
                </button>
                <a href="{{route('sdt-training')}}" class="btn btn-secondary waves-effect">Kembali</a>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('pageScript')
<script>
$(document).ready(function() {
    initializeSelect2();
    
    let currentStep = 1;
    const totalSteps = 7;
    
    updateFormDisplay();
    
    $('#next-btn').on('click', function() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                updateFormDisplay();
                loadStepData();
            }
        }
    });
    
    $('#prev-btn').on('click', function() {
        if (currentStep > 1) {
            currentStep--;
            updateFormDisplay();
        }
    });
    
    $('#laman_id').on('change', function() {
        const selectedValue = this.value;
        if (selectedValue) {
            loadAreas(selectedValue);
        } else {
            resetSelect('#area_id', '- Pilih Business Unit dulu -');
            resetSelect('#client_id', '- Pilih Area dulu -');
        }
    });
    
    $('#area_id').on('change', function() {
        if (this.value) {
            resetSelect('#client_id', '- Pilih client... -');
        } else {
            resetSelect('#client_id', '- Pilih Area dulu -');
        }
    });
    
    $('#start_date').on('change', function() {
        const startDate = new Date(this.value);
        const endDateInput = document.getElementById('end_date');
        
        if (endDateInput.value) {
            const endDate = new Date(endDateInput.value);
            if (startDate >= endDate) {
                endDateInput.value = '';
                showAlert('Waktu selesai harus lebih besar dari waktu mulai', 'warning');
            }
        }
        
        endDateInput.min = this.value;
    });
    
    $('#end_date').on('change', function() {
        const endDate = new Date(this.value);
        const startDate = new Date(document.getElementById('start_date').value);
        
        if (endDate <= startDate) {
            this.value = '';
            showAlert('Waktu selesai harus lebih besar dari waktu mulai', 'warning');
        }
    });
    
    function initializeSelect2() {
        $('.select2').each(function() {
            const $this = $(this);
            const config = {
                width: '100%',
                allowClear: $this.data('allow-clear') || false,
                placeholder: $this.find('option:first').text() || 'Pilih...',
                minimumResultsForSearch: $this.find('option').length > 10 ? 0 : -1
            };
            
            if ($this.prop('multiple')) {
                config.closeOnSelect = false;
            }
            
            $this.select2(config);
        });
    }
    
    function updateFormDisplay() {
        $('.form-step').removeClass('active').hide();
        $(`.form-step[data-step="${currentStep}"]`).addClass('active').show();
        
        const progress = (currentStep / totalSteps) * 100;
        $('#form-progress').css('width', progress + '%');
        $('#current-step').text(currentStep);
        
        // Logika perbaikan untuk tombol navigasi
        if (currentStep === 1) {
            $('#prev-btn').hide();
        } else {
            $('#prev-btn').show();
        }
        
        if (currentStep === totalSteps) {
            $('#next-btn').addClass('hidden-button');
            $('#submit-btn').removeClass('hidden-button');
            showFormSummary();
        } else {
            $('#next-btn').removeClass('hidden-button');
            $('#submit-btn').addClass('hidden-button');
        }
    }
    
    function validateCurrentStep() {
        let isValid = true;
        const currentStepElement = $(`.form-step[data-step="${currentStep}"]`);
        
        currentStepElement.find('.is-invalid').removeClass('is-invalid');
        
        currentStepElement.find('[required]').each(function() {
            const $field = $(this);
            const value = $field.val();
            
            // Perbaikan validasi untuk field yang kosong
            if (!value || (Array.isArray(value) && value.length === 0)) {
                $field.addClass('is-invalid');
                isValid = false;
            }
        });
        
        if (!isValid) {
            showAlert('Mohon lengkapi semua field yang wajib diisi', 'error');
        }
        
        return isValid;
    }
    
    function loadStepData() {
        switch(currentStep) {
            case 2:
                const buText = $('#laman_id option:selected').text();
                const areaText = $('#area_id option:selected').text();
                $('#debug-bu').text(buText || 'Belum dipilih');
                $('#debug-area').text(areaText || 'Belum dipilih');
                
                const areaId = $('#area_id').val();
                if (areaId) {
                    loadClients(areaId);
                } else {
                    currentStep = 1;
                    updateFormDisplay();
                    showAlert('Silakan pilih Area terlebih dahulu', 'warning');
                }
                break;
        }
    }
    
    function loadAreas(lamanId) {
        const $area = $('#area_id');
        $area.empty().append('<option value="">- Loading areas... -</option>');
        refreshSelect2($area);
        
        $.ajax({
            type: 'GET',
            url: "{{route('sdt-training.list-area')}}",
            data: { 'id': lamanId },
            success: function (response) {
                $area.empty();
                $area.append('<option value="">- Pilih Area -</option>');
                
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        $area.append(`<option value="${item.id}">${item.area}</option>`);
                    });
                }
                refreshSelect2($area);
            },
            error: function(xhr, status, error) {
                console.error('Error loading areas:', error);
                showAlert('Gagal memuat data area', 'error');
                resetSelect('#area_id', '- Error loading areas -');
            }
        });
    }
    
    function loadClients(areaId) {
        const lamanId = $('#laman_id').val();
        const $client = $('#client_id');
        $client.empty().append('<option value="">- Loading clients... -</option>');
        refreshSelect2($client);
        
        $.ajax({
            type: 'GET',
            url: "{{route('sdt-training.list-client')}}",
            data: { 
                'area_id': areaId,
                'laman_id': lamanId
            },
            success: function (response) {
                $client.empty();
                if (response && response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        $client.append(`<option value="${item.id}">${item.client}</option>`);
                    });
                } else {
                    $client.append('<option value="" disabled>- Tidak ada client tersedia -</option>');
                }
                refreshSelect2($client);
            },
            error: function(xhr, status, error) {
                console.error('Error loading clients:', error);
                showAlert('Gagal memuat data client: ' + error, 'error');
                resetSelect('#client_id', '- Error loading clients -');
            }
        });
    }
    
    function resetSelect(selector, message) {
        const $select = $(selector);
        $select.empty().append(`<option value="">${message}</option>`);
        refreshSelect2($select);
    }
    
    function refreshSelect2($select) {
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }
        const config = {
            width: '100%',
            allowClear: $select.data('allow-clear') || false,
            placeholder: $select.find('option:first').text() || 'Pilih...',
            minimumResultsForSearch: $select.find('option').length > 10 ? 0 : -1
        };
        if ($select.prop('multiple')) {
            config.closeOnSelect = false;
        }
        $select.select2(config);
    }
    
    function showFormSummary() {
        const summary = [];
        
        const buText = $('#laman_id option:selected').text();
        const areaText = $('#area_id option:selected').text();
        if (buText && areaText && buText !== '- Pilih Business Unit -' && areaText !== '- Pilih Area -') {
            summary.push(`<strong>Business Unit:</strong> ${buText}`);
            summary.push(`<strong>Area:</strong> ${areaText}`);
        }
        
        const clientTexts = $('#client_id option:selected').map(function() { return $(this).text(); }).get();
        if (clientTexts.length > 0) {
            summary.push(`<strong>Client:</strong> ${clientTexts.join(', ')}`);
        }
        
        const trainerTexts = $('#trainer_id option:selected').map(function() { return $(this).text(); }).get();
        if (trainerTexts.length > 0) {
            summary.push(`<strong>Trainer:</strong> ${trainerTexts.join(', ')}`);
        }
        
        const materiText = $('#materi_id option:selected').text();
        if (materiText && materiText !== '- Pilih Materi -') {
            summary.push(`<strong>Materi:</strong> ${materiText}`);
        }
        
        const tempatText = $('#tempat_id option:selected').text();
        if (tempatText && tempatText !== '- Pilih Tempat -') {
            summary.push(`<strong>Tempat:</strong> ${tempatText}`);
        }
        
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        if (startDate && endDate) {
            summary.push(`<strong>Jadwal:</strong> ${formatDateTime(startDate)} s/d ${formatDateTime(endDate)}`);
        }
        
        $('#summary-content').html(summary.join('<br>'));
    }
    
    function formatDateTime(dateTimeString) {
        const date = new Date(dateTimeString);
        return date.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    function showAlert(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        
        $('.alert:not(#debug-info):not(#form-summary .alert)').remove();
        $('.container-fluid').prepend(alertHtml);
        
        setTimeout(() => {
            $('.alert:not(#debug-info):not(#form-summary .alert)').fadeOut();
        }, 5000);
    }
    
    $('#training-form').on('submit', function(e) {
        if (!validateCurrentStep()) {
            e.preventDefault();
        }
    });
});
</script>

<script>
  @if(session()->has('success'))  
    Swal.fire({
      title: 'Pemberitahuan',
      html: '{{session()->get('success')}}',
      icon: 'success',
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
      },
      buttonsStyling: false
    }).then(function() {
        window.location.href = '{{route("sdt-training")}}';
    });
  @endif
  
  @if(session()->has('error'))  
    Swal.fire({
      title: 'Error',
      html: '{{session()->get('error')}}',
      icon: 'error',
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
      },
      buttonsStyling: false
    });
  @endif
</script>

<style>
.form-step {
    display: none;
}
.form-step.active {
    display: block;
}
.progress {
    background-color: #e9ecef;
}
.progress-bar {
    background-color: #696cff;
    transition: width 0.3s ease;
}
.h-px-150 {
    height: 150px !important;
}

/* New style to forcefully hide the button */
.hidden-button {
    display: none !important;
}
</style>
@endsection