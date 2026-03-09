<x-app-layout title="Crediti — Tomodachi tracker">

<div class="px-2 px-md-4 px-lg-5 d-flex justify-content-center">
    <div class="card shadow-lg p-4 p-md-5" style="max-width:680px;width:100%;">

        <h1 class="mb-4 text-center">Crediti</h1>

        {{-- Programmatore --}}
        <section class="mb-4">
            <h5 class="text-muted text-uppercase fw-semibold mb-3" style="letter-spacing:.08em;font-size:.8rem;">
                Programmatore
            </h5>
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:56px;height:56px;font-size:1.3rem;color:#fff;">DP</div>
                <div>
                    <p class="mb-0 fw-semibold fs-5">Davide Piovesan</p>
                    <div class="d-flex gap-2 flex-wrap mt-1">
                        <a href="https://github.com/Piovide" target="_blank" rel="noopener"
                           class="btn btn-sm btn-dark">
                            <x-icon name="github" size="sm" /> @Piovide
                        </a>
                        <a href="https://ko-fi.com/Piovide" target="_blank" rel="noopener"
                           class="btn btn-sm btn-danger">
                            <img src="https://storage.ko-fi.com/cdn/logomarkLogo.png"
                                 alt="Ko-fi" style="width:16px;vertical-align:middle; margin-right: 5px" class="d-inline"> Ko-fi
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <hr>

        {{-- Artista logo --}}
        <section class="mb-4">
            <h5 class="text-muted text-uppercase fw-semibold mb-3" style="letter-spacing:.08em;font-size:.8rem;">
                Artista Logo
            </h5>
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:56px;height:56px;font-size:1.3rem;color:#fff;">TF</div>
                <div>
                    <p class="mb-0 fw-semibold fs-5">Tommaso Franceschet</p>
                    <p class="mb-0 text-muted small">Autore del logo</p>
                </div>
            </div>
        </section>

        <hr>

        {{-- Tecnologie --}}
        <section>
            <h5 class="text-muted text-uppercase fw-semibold mb-3" style="letter-spacing:.08em;font-size:.8rem;">
                Realizzato con
            </h5>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-danger fs-6 px-3 py-2">Laravel</span>
                <span class="badge bg-primary fs-6 px-3 py-2">Bootstrap 5</span>
                <span class="badge bg-dark fs-6 px-3 py-2">Vite</span>
                <span class="badge bg-secondary fs-6 px-3 py-2">PostgreSQL</span>
            </div>
        </section>

    </div>
</div>

</x-app-layout>
