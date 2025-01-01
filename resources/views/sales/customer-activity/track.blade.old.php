@extends('layouts.master')
@section('title','Dashboard Aktifitas Sales')
@section('pageStyle')
  <style>
.card {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: none;
}

.card:hover {
  transform: translateY(-10px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.line {
  background-color: #6c757d;
}

.line.horizontal {
  height: 4px;
  width: 100%;
}

.line.horizontal.reverse {
  height: 4px;
  width: 100%;
}

.line .vertical {
  width: 4px;
  height: 100%;
  margin: 0 auto;
}

.d-flex {
  display: flex;
}

.justify-content-center {
  justify-content: center;
}

.align-items-center {
  align-items: center;
}

.mb-4 {
  margin-bottom: 1.5rem;
}

/* Adjust card widths to be more flexible */
.card.node {
  width: 150px;
  text-align: center;
}

.card-body {
  padding: 1.25rem;
}

/* Responsiveness for Mobile */
@media (max-width: 768px) {
  .line.horizontal, .line.horizontal.reverse {
    display: none; /* Hide horizontal lines on mobile */
  }

  .line .vertical {
    width: 4px;
    height: 50px; /* Adjust height to make the vertical line shorter on mobile */
    margin: 0 auto;
  }

  .row {
    flex-direction: column; /* Stack the elements vertically on small screens */
    align-items: center;
  }

  .col-md-2, .col-md-1 {
    margin-bottom: 20px; /* Space out the elements more on small screens */
  }

  .card.node {
    width: 80%; /* Make the card smaller on mobile to fit better */
  }
}
  </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-12 mb-4">
            <div class="container py-5">
                <div class="row">
                    <div class="col-md-2 mb-4 d-flex justify-content-center">
                        <div class="card node">
                        <div class="card-body">
                            <h5 class="card-title">Start</h5>
                            <p class="card-text">Process begins here.</p>
                            <small class="text-muted">01-Jan-2025</small>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex justify-content-center align-items-center">
                        <div class="line horizontal"></div>
                    </div>
                    <div class="col-md-2 mb-4 d-flex justify-content-center">
                        <div class="card node">
                        <div class="card-body">
                            <h5 class="card-title">Quotation</h5>
                            <p class="card-text">Generate and send quotation to client.</p>
                            <small class="text-muted">02-Jan-2025</small>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex justify-content-center align-items-center">
                        <div class="line horizontal"></div>
                    </div>
                    <div class="col-md-2 mb-4 d-flex justify-content-center">
                        <div class="card node">
                        <div class="card-body">
                            <h5 class="card-title">Negotiation</h5>
                            <p class="card-text">Discuss terms and finalize agreement.</p>
                            <small class="text-muted">03-Jan-2025</small>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex justify-content-center align-items-center">
                        <div class="line horizontal"></div>
                    </div>
                    <div class="col-md-2 mb-4 d-flex justify-content-center">
                        <div class="card node">
                        <div class="card-body">
                            <h5 class="card-title">Negotiation</h5>
                            <p class="card-text">Discuss terms and finalize agreement.</p>
                            <small class="text-muted">03-Jan-2025</small>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1 d-flex justify-content-end align-items-end">
                        <div class="line vertical"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 mb-4 d-flex justify-content-center">
                        <div class="card node">
                        <div class="card-body">
                            <h5 class="card-title">Approval</h5>
                            <p class="card-text">Approve final terms and proceed.</p>
                            <small class="text-muted">04-Jan-2025</small>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex justify-content-center align-items-center">
                        <div class="line horizontal reverse"></div>
                    </div>
                    <div class="col-md-2 mb-4 d-flex justify-content-center">
                        <div class="card node">
                        <div class="card-body">
                            <h5 class="card-title">Invoice</h5>
                            <p class="card-text">Generate invoice and send to client.</p>
                            <small class="text-muted">05-Jan-2025</small>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex justify-content-center align-items-center">
                        <div class="line horizontal reverse"></div>
                    </div>
                    <div class="col-md-2 mb-4 d-flex justify-content-center">
                        <div class="card node">
                        <div class="card-body">
                            <h5 class="card-title">End</h5>
                            <p class="card-text">Process completed successfully.</p>
                            <small class="text-muted">06-Jan-2025</small>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex justify-content-center align-items-center">
                        <div class="line horizontal reverse"></div>
                    </div>
                    <div class="col-md-2 mb-4 d-flex justify-content-center">
                        <div class="card node">
                        <div class="card-body">
                            <h5 class="card-title">End</h5>
                            <p class="card-text">Process completed successfully.</p>
                            <small class="text-muted">06-Jan-2025</small>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageScript')
<script>
    document.querySelectorAll('.node').forEach(node => {
  node.addEventListener('click', () => {
    alert(`${node.textContent} clicked!`);
  });
});
  </script>
@endsection

