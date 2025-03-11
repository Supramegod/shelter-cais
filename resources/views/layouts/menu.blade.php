<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
    <div class="container-fluid d-flex h-100">
    <ul class="menu-inner">
        <li class="menu-item @if(Request::url() === route('home')||Request::url() === route('dashboard')) active @endif">
            <a href="{{route('home')}}" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
            <div data-i18n="Dashboards">Home</div>
            </a>
        </li>
        @if(in_array(Auth::user()->role_id,[2,31,32,33,52,54,55,56,53,56,96,97,40,98,99,100]))
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-chart-box-outline"></i>
                <div data-i18n="Master Data">Dashboard</div>
            </a>
            <ul class="menu-sub">
                @if(in_array(Auth::user()->role_id,[2,52,54,55,56,53,56,96,97,40,98,99,100]))
                <li class="menu-item @if(str_contains(Request::url(), route('dashboard-approval'))) active @endif">
                    <a href="{{route('dashboard-approval')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Dashboard Approval">Approval</div>
                    </a>
                </li>
                @endif
                @if(in_array(Auth::user()->role_id,[2]))
                <li class="menu-item @if(str_contains(Request::url(), route('dashboard-general'))) active @endif">
                    <a href="{{route('dashboard-general')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div>General</div>
                    </a>
                </li>
                @endif
                @if(in_array(Auth::user()->role_id,[2,31,32,33,52,53,56,96,97,40,98,99,100]))
                <li class="menu-item @if(str_contains(Request::url(), route('dashboard-aktifitas-sales'))) active @endif">
                    <a href="{{route('dashboard-aktifitas-sales')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div>Aktifitas Sales</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('dashboard-aktifitas-telesales'))) active @endif">
                    <a href="{{route('dashboard-aktifitas-telesales')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div>Aktifitas Telesales</div>
                    </a>
                </li>
                @endif
                @if(in_array(Auth::user()->role_id,[2,52,53,56,96,97,40,98,99,100]))
                <li class="menu-item @if(str_contains(Request::url(), route('dashboard-leads'))) active @endif">
                    <a href="{{route('dashboard-leads')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div>Leads</div>
                    </a>
                </li>
                @endif
                <li class="menu-item @if(str_contains(Request::url(), route('dashboard-leads'))) active @endif">
                    <a href="{{route('dashboard-sdt-training')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div>Dashboard SDT Training</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(in_array(Auth::user()->role_id,[2,48,29,30,31,33,52,96,97,40,99]))
        <li class="menu-item @if(str_contains(Request::url(), route('customer')) && str_contains(Request::url(), 'customer-activity')==false)) active @endif">
            <a href="{{route('customer')}}" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-account-star-outline"></i>
            <div data-i18n="Customer">Customer</div>
            </a>
        </li>
        @endif
        @if(in_array(Auth::user()->role_id,[2,48,29,30,31,33,52,96,40,99]))
        <li class="menu-item @if(str_contains(Request::url(), route('leads'))) active @endif">
            <a href="{{route('leads')}}" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-account-search-outline"></i>
            <div data-i18n="Leads">Leads</div>
            </a>
        </li>
        @endif
        @if(in_array(Auth::user()->role_id,[2,48,29,30,31,33,52,96,97,40,99]))
        <li class="menu-item @if(str_contains(Request::url(), route('site'))) active @endif">
            <a href="{{route('site')}}" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-account-search-outline"></i>
            <div data-i18n="Site">Site</div>
            </a>
        </li>
        @endif
        @if(in_array(Auth::user()->role_id,[2,8,56,29,30,31,33,52,96,97,40,99]))
        <li class="menu-item @if(str_contains(Request::url(), route('customer-activity'))) active @endif">
            <a href="{{route('customer-activity')}}" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-calendar-check-outline"></i>
            <div data-i18n="Customer Activity">Customer Activity</div>
            </a>
        </li>
        @endif
        @if(in_array(Auth::user()->role_id,[2,29,31,33]))
        <li class="menu-item @if(str_contains(Request::url(), route('quotation'))) active @endif">
            <a href="{{route('quotation')}}" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-invoice-list-outline"></i>
            <div data-i18n="Quotation">Quotation</div>
            </a>
        </li>
        @endif
        @if(in_array(Auth::user()->role_id,[2,29,31,33,56]))
        <li class="menu-item @if(str_contains(Request::url(), route('spk'))) active @endif">
            <a href="{{route('spk')}}" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-format-list-text"></i>
            <div data-i18n="SPK">SPK</div>
            </a>
        </li>
        @endif
        @if(in_array(Auth::user()->role_id,[2,29,31,33,56]))
        <li class="menu-item @if(str_contains(Request::url(), route('pks'))) active @endif">
            <a href="{{route('pks')}}" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-file-sign"></i>
            <div data-i18n="PKS">PKS</div>
            </a>
        </li>
        @endif
        @if(in_array(Auth::user()->role_id,[2,29,31,33,54,55,4,5,6,8,52,53,10,96,97,40,99]))
        <li class="menu-item @if(str_contains(Request::url(), route('monitoring-kontrak'))) active @endif">
            <a href="{{route('monitoring-kontrak')}}" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-file-cabinet"></i>
            <div data-i18n="Monitoring Kontrak">Monitoring Kontrak</div>
            </a>
        </li>
        @endif
        @if(in_array(Auth::user()->role_id,[2]))
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-account-search-outline"></i>
                <div data-i18n="Kontrak">Training</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{route('sdt-training')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Training Data">Training Data</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{route('training-site')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Training Site">Training Site</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif
        @if(in_array(Auth::user()->role_id,[2]))
        <!-- MASTER DATA -->
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-database-outline"></i>
                <div data-i18n="Master Data">Master Data</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item @if(str_contains(Request::url(), route('kebutuhan'))) active @endif">
                    <a href="{{route('kebutuhan')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Kebutuhan">Kebutuhan</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('tim-sales'))) active @endif">
                    <a href="{{route('tim-sales')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Tim Sales">Tim Sales</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('training'))) active @endif">
                    <a href="{{route('training')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Training">Training</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('training-materi'))) active @endif">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Training Materi">Training SDT</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item @if(str_contains(Request::url(), route('training-materi'))) active @endif">
                        <a href="{{route('training-materi')}}" class="menu-link">
                            <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                            <div data-i18n="Training Materi">Training Materi</div>
                        </a>
                    </li>

                    <li class="menu-item @if(str_contains(Request::url(), route('training-divisi'))) active @endif">
                        <a href="{{route('training-divisi')}}" class="menu-link">
                            <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                            <div data-i18n="Training Divisi">Training Divisi</div>
                        </a>
                    </li>

                    <li class="menu-item @if(str_contains(Request::url(), route('training-trainer'))) active @endif">
                        <a href="{{route('training-trainer')}}" class="menu-link">
                            <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                            <div data-i18n="Training Trainer">Training Trainer</div>
                        </a>
                    </li>

                    <li class="menu-item @if(str_contains(Request::url(), route('training-area'))) active @endif">
                        <a href="{{route('training-area')}}" class="menu-link">
                            <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                            <div data-i18n="Training Area">Training Area</div>
                        </a>
                    </li>

                    <li class="menu-item @if(str_contains(Request::url(), route('training-client'))) active @endif">
                        <a href="{{route('training-client')}}" class="menu-link">
                            <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                            <div data-i18n="Training Client">Training Client</div>
                        </a>
                    </li>
                    </ul>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('whatsapp'))) active @endif">
                    <a href="{{route('whatsapp')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="whatsapp">Whatsapp</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('platform'))) active @endif">
                    <a href="{{route('platform')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Sumber Leads">Sumber Leads</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('jenis-visit'))) active @endif">
                    <a href="{{route('jenis-visit')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Jenis Visit">Jenis Visit</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('aplikasi-pendukung'))) active @endif">
                    <a href="{{route('aplikasi-pendukung')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Aplikasi Pendukung">Aplikasi Pendukung</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('barang'))) active @endif">
                    <a href="{{route('barang')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Barang">Barang</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('jenis-barang'))) active @endif">
                    <a href="{{route('jenis-barang')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Jenis Barang">Jenis Barang</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('jabatan'))) active @endif">
                    <a href="{{route('jabatan')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Jabatan PIC">Jabatan PIC</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('perusahaan'))) active @endif">
                    <a href="{{route('perusahaan')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Jenis Perusahaan">Jenis Perusahaan</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('management-fee'))) active @endif">
                    <a href="{{route('management-fee')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Management Fee">Management Fee</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('salary-rule'))) active @endif">
                    <a href="{{route('salary-rule')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Salary Rule">Salary Rule</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('status-leads'))) active @endif">
                    <a href="{{route('status-leads')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Status Leads">Status Leads</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('tunjangan'))) active @endif">
                    <a href="{{route('tunjangan')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Tunjangan">Tunjangan</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('ump'))) active @endif">
                    <a href="{{route('ump')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="UMP">UMP</div>
                    </a>
                </li>
                <li class="menu-item @if(str_contains(Request::url(), route('umk'))) active @endif">
                    <a href="{{route('umk')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="UMK">UMK</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-list-box"></i>
                <div data-i18n="Log">Log</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item @if(str_contains(Request::url(), route('notifikasi'))) active @endif">
                    <a href="{{route('notifikasi')}}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div>Notifikasi</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif


















        <!-- <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-invoice-list-outline"></i>
            <div data-i18n="Quotation">Quotation</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-format-list-text"></i>
            <div data-i18n="SPK">SPK</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-file-sign"></i>
            <div data-i18n="PKS">PKS</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-file-refresh-outline"></i>
            <div data-i18n="Adendum">Adendum</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons mdi mdi-page-next-outline"></i>
            <div data-i18n="Form Lanjutan">Form Lanjutan</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Quotation Lanjutan">Quotation Lanjutan</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="SPK Lanjutan">SPK Lanjutan</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="SPK Lanjutan">PKS Lanjutan</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="SPK Lanjutan">Adendum Lanjutan</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi mdi-file-cabinet"></i>
                <div data-i18n="Kontrak">Kontrak</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Monitoring Kontrak">Monitoring Kontrak</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-circle-medium"></i>
                        <div data-i18n="Terminate Kontrak">Terminate Kontrak</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-chart-areaspline"></i>
            <div data-i18n="Report">Report</div>
            </a>
        </li> -->
    </ul>
    </div>
</aside>
