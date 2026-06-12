<x-layout title="Landing Page">

    <section class="bg-dark text-white py-5" > {{-- //*  bg-dark --}}
        <div class="container-fluid">
            <div class="row mx-4 align-items-center min-vh-100">
                <div class="col-lg-6">

                    <h1 class="display-3 fw-bold mb-4">
                        Organize your studies
                    </h1>

                    <p class="lead mb-4">
                        Make schedules, events and even invite friends to them!
                    </p>

                    <div class="d-flex gap-3">
                        @guest
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                            Log in
                        </a>

                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                            Register
                        </a>
                        @endguest
                    </div>
                </div>

                <div class="col-lg-6 text-center mt-5 mt-lg-0">
                    <img
                        src="{{ asset('images/preview-2.png') }}"
                        class="img-fluid rounded shadow-lg"
                        alt="Product Preview"
                    >
                </div>
            </div>
        </div>
    </section>
    

    <section class="py-5 text-center" style="background-color:#D6D5E1;">
        <div class="container-fluid">
            <h2 class="fw-bold mb-4">
                Start making schedules now!!!!
            </h2>
            @guest
            <a href="{{ route('register') }}" class="btn btn-lg px-5 border border-secondary rounded border-3 btn-outline-light btn-lg" style="background-color:#AE7D9A;">
                Create an Account 
            </a>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-top py-4" style="background-color:#767499;">
        <div class="container-fluid text-center">
            <small class="text-black fw-bold">
                {{ date('Y') }} Toms Gerbaševskis tg25032
            </small>
        </div>
    </footer>

</x-layout>