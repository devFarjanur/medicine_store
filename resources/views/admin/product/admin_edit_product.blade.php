@extends('admin.admin_dashboard')

@section('admin')


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.product') }}">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h3 class="mb-3">Edit Product</h3>
                    <!-- <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">Primary</button>
                </div> -->

                    <form method="POST" action="{{ route('admin.update.product', $product->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')


                        <div class="mb-3">
                            <label class="form-label">Product Photo</label>
                            <input name="photo" type="file" class="form-control" id="image" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <img id="showImage" class="wd-150 rounded" height="150px"
                                src="{{ $product->photo ? asset('upload/admin_images/' . $product->photo) : url('upload/no_image.jpg') }}"
                                alt="profile">
                        </div>

                        <div class="form-group mb-3">
                            <label for="category" class="form-label">Product Category</label>
                            <select class="form-control" id="category" name="category_id">
                                <option value="" selected disabled>-- Select Product Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="name" id="name" autocomplete="off"
                                placeholder="Name" value="{{ $product->name }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Price</label>
                            <input type="text" class="form-control" name="price" id="price" placeholder="Price"
                                value="{{ $product->price }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Stock</label>
                            <input type="text" class="form-control" name="stock" id="stock" placeholder="Stock"
                                value="{{ $product->stock }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Description</label>
                            <textarea id="description" name="description" class="form-control" rows="8"
                                placeholder="Enter a detailed description of your product...">{{ $product->description }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary me-2">Update Product</button>
                        <button class="btn btn-secondary">Cancel</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#image').change(function (e) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#showImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });
    </script>


    @endsection