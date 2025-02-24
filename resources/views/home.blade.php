@extends('main')
@section('meta_title', 'Home')

@section('content')
    <div class="container py-5">
        <div id="carouselExample" class="carousel slide mb-4">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://placehold.co/1200x400" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="https://placehold.co/1200x400" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="https://placehold.co/1200x400" class="d-block w-100" alt="...">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <p>
            Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Mauris felis felis,
            semper vitae interdum quis, eleifend vel nisl. Nam tempus augue eu tortor euismod porttitor. Suspendisse at
            turpis ac augue pellentesque semper. Duis pellentesque dignissim metus, a porttitor sem euismod in. Donec ac
            mauris non augue bibendum auctor. Integer semper odio tristique pretium dapibus. Quisque gravida eleifend enim,
            sed pretium mauris. Aenean hendrerit metus a leo laoreet laoreet. Lorem ipsum dolor sit amet, consectetur
            adipiscing elit. Pellentesque luctus, sapien in iaculis commodo, orci erat auctor justo, non egestas odio felis
            sed nisi. Ut ullamcorper blandit elit, in tincidunt augue ultrices eu. Sed maximus condimentum ante, ut
            elementum purus placerat eu. Nullam nec arcu sed nisl consequat pharetra in bibendum purus. Nam quis turpis at
            ligula egestas vulputate tempus at lacus.
        </p>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Launch demo modal
        </button>
    </div>
@endsection
