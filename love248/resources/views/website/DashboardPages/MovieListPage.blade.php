@extends('layouts.app', ['module_title' => 'Movie List'])

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card pb-3">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center pb-3">
                    <div class="d-flex align-items-center">
                        <!-- Replace the Vue component with HTML -->
                        <div class="form-group">
                            <select type="select" class="form-control select2-basic-multiple" placeholder="No Action">
                                <option>No Action</option>
                                <option>Status</option>
                                <option>Delete</option>
                            </select>
                            <button class="btn btn-secondary">Apply</button>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#season-offcanvas" aria-controls="season-offcanvas">
                        <i class="fa-solid fa-plus me-2" style="color: #fafcff;"></i>
                        Add Movie
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-view table-responsive pt-3">
                        <table id="seasonTable" class="data-tables table movie_table" data-toggle="data-table">
                            <thead>
                                <tr class="text-uppercase">
                                    <th class="text-center">
                                        <input type="checkbox" class="form-check-input" />
                                    </th>
                                    <th>Movie</th>
                                    <th>Quality</th>
                                    <th>Category</th>
                                    <th>Publish Date</th>
                                    <th>Movie Access</th>
                                    <th>Seo</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @include('components.datatable.DataTable', [
                                    'name' => '1980',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/01.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['fight', 'thriller', 'etc'],
                                    'date' => '2013',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'Boop Bitty',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/02.jpg'),
                                    'quality' => '480/1080',
                                    'duration' => '2h 40m',
                                    'genres' => ['action', 'thriller', 'etc'],
                                    'date' => '2012',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'Burning',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/03.jpg'),
                                    'quality' => '720/1080',
                                    'duration' => '3h',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'Last Night',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/04.jpg'),
                                    'quality' => '480',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'Champions',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/05.jpg'),
                                    'quality' => '1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'thriller', 'etc'],
                                    'date' => '2014',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'Dino Land',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/06.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2011',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'Last Race',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/07.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2015',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'Looters',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/08.jpg'),
                                    'quality' => '720',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2016',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'The Illution',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/09.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'The Last Breath',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/10.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Offcanvas Form -->
            <div class="offcanvas offcanvas-end offcanvas-width-80 on-rtl end" tabindex="-1" id="season-offcanvas"
                aria-labelledby="season-offcanvas-lable">
                <div class="offcanvas-header">
                    <h5 id="offcanvasRightLabel1">Add New Season</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">

                    <form>
                        <div class="section-form">
                            <fieldset>
                                <legend>Movie</legend>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group px-3 ">
                                            <label class="form-label flex-grow-1" for="Movie Name">
                                                <strong>Movie Name</strong> <span class="text-danger">*</span>:
                                            </label>

                                            <!-- textarea input -->
                                            <!-- toggle switch -->
                                            <!-- common inputs -->
                                            <input id="Movie Name" type="text" class="form-control "
                                                placeholder="Enter Movie Name" min="" multiple="">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group px-3 ">
                                            <label class="form-label flex-grow-1" for="Description">
                                                <strong>Description</strong> :
                                            </label>

                                            <!-- textarea input -->
                                            <textarea id="Description" class="form-control" placeholder="Description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group px-3">
                                            <label class="form-label flex-grow-1" for="movie-access"><strong>Movie
                                                    Access:</strong></label>
                                            <select id="movie-access" type="select"
                                                class="form-control select2-basic-multiple"
                                                placeholder="select movie access" tabindex="0" aria-hidden="false">
                                                <option>Free</option>
                                                <option>standard</option>
                                                <option>premium</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group px-3">
                                            <label class="form-label flex-grow-1"
                                                for="language"><strong>Language:</strong></label>
                                            <select id="language" type="select"
                                                class="form-control select2-basic-multiple" placeholder="select language"
                                                tabindex="0" aria-hidden="false">
                                                <option>Hindi</option>
                                                <option>English</option>
                                                <option>French</option>
                                                <option>Marathi</option>
                                                <option>Gujrati</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group px-3">
                                            <label class="form-label flex-grow-1"
                                                for="genres"><strong>Genres:</strong></label>
                                            <select id="genres" type="select"
                                                class="form-control select2-basic-multiple" placeholder="select genres"
                                                tabindex="0" aria-hidden="false">
                                                <option>Action</option>
                                                <option>Adventure</option>
                                                <option>Animation</option>
                                                <option>Horror</option>
                                                <option>Thriller</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center my-5 px-3">
                                    <h5>
                                        <strong>Casts / Crews</strong>
                                    </h5>
                                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal"
                                        data-bs-target="#cast-modal">
                                        <i class="fa-solid fa-square-plus me-2"></i>Add Cast / Crew
                                    </button>

                                    <div class="modal fade" id="cast-modal" tabindex="-1" role="dialog"
                                        aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="cast-modal-label">Add</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group px-3 ">
                                                                <label class="form-label flex-grow-1" for="Person">
                                                                    <strong>Person</strong> :
                                                                </label>

                                                                <!-- textarea input -->
                                                                <!-- toggle switch -->
                                                                <!-- common inputs -->
                                                                <input id="Person" type="text"
                                                                    class="form-control " placeholder="Enter Name"
                                                                    min="" multiple="">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group px-3 d-flex flex-column">
                                                                <label class="form-label flex-grow-1"
                                                                    for="occupation"><strong>Occupation:</strong></label>
                                                                <select id="occupation" type="select"
                                                                    class="form-control select2-basic-multiple"
                                                                    placeholder="Select Occupation" tabindex="0"
                                                                    aria-hidden="false">
                                                                    <option>Cast</option>
                                                                    <option>Crew</option>
                                                                    <option>Production</option>
                                                                    <option>Director</option>
                                                                    <option>Actor</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group px-3 ">
                                                                <label class="form-label flex-grow-1" for="As">
                                                                    <strong>As</strong> :
                                                                </label>

                                                                <!-- textarea input -->
                                                                <!-- toggle switch -->
                                                                <!-- common inputs -->
                                                                <input id="As" type="text"
                                                                    class="form-control " placeholder="Played as"
                                                                    min="" multiple="">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group px-3 d-flex justify-content-between">
                                                                <label class="form-label flex-grow-1" for="Status">
                                                                    <strong>Status</strong> :
                                                                </label>

                                                                <!-- textarea input -->
                                                                <!-- toggle switch -->
                                                                <div class="form-check form-switch ms-2">
                                                                    <input id="Status" class="form-check-input"
                                                                        type="checkbox">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-3">
                                    <div>
                                        <table class="table table-bordered table-strip">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Cast/Crew</th>
                                                    <th>Name</th>
                                                    <th>Role</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="text-center">
                                                    <td>Cast</td>
                                                    <td>ABC</td>
                                                    <td>James</td>
                                                    <td>
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <a aria-current="page" href="#"
                                                                class="active text-success" title="Edit">
                                                                <i class="fa-solid fa-pen mx-4"></i>
                                                            </a>
                                                            <a aria-current="page" href="#"
                                                                class="active text-danger" title="Delete">
                                                                <i class="fa-solid fa-trash me-4"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="text-center">
                                                    <td>Crew</td>
                                                    <td>XYZ</td>
                                                    <td>Producer</td>
                                                    <td>
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <a aria-current="page" href="#"
                                                                class="active text-success" title="Edit">
                                                                <i class="fa-solid fa-pen mx-4"></i>
                                                            </a>
                                                            <a aria-current="page" href="#"
                                                                class="active text-danger" title="Delete">
                                                                <i class="fa-solid fa-trash me-4"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="col-lg-3">
                                        <div class="form-group px-3 ">
                                            <label class="form-label flex-grow-1" for="Content Rating">
                                                <strong>Content Rating</strong> :
                                            </label>

                                            <!-- textarea input -->
                                            <!-- toggle switch -->
                                            <!-- common inputs -->
                                            <input id="Content Rating" type="text" class="form-control "
                                                placeholder="Rating" min="" multiple="">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group px-3">
                                            <label class="form-label flex-grow-1" for="genres"><strong>Release
                                                    Date:</strong></label>
                                            <input class="form-control flatpickr_humandate flatpickr-input" type="hidden"
                                                placeholder="release date.." data-id="multiple"><input
                                                class="form-control flatpickr_humandate form-control input"
                                                placeholder="release date.." tabindex="0" type="text"
                                                readonly="readonly">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group px-3">
                                            <label class="form-label flex-grow-1" for="genres"><strong>Publice
                                                    Date:</strong></label>
                                            <input class="form-control flatpickr_humandate flatpickr-input" type="hidden"
                                                placeholder="publice date.." data-id="multiple"><input
                                                class="form-control flatpickr_humandate form-control input"
                                                placeholder="publice date.." tabindex="0" type="text"
                                                readonly="readonly">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group px-3 ">
                                            <label class="form-label flex-grow-1" for="Duration">
                                                <strong>Duration</strong> :
                                            </label>

                                            <!-- textarea input -->
                                            <!-- toggle switch -->
                                            <!-- common inputs -->
                                            <input id="Duration" type="text" class="form-control "
                                                placeholder="Duration in mins" min="" multiple="">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>SEO</legend>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group px-3 ">
                                            <label class="form-label flex-grow-1" for="SEO Title">
                                                <strong>SEO Title</strong> <span class="text-danger">*</span>:
                                            </label>

                                            <!-- textarea input -->
                                            <!-- toggle switch -->
                                            <!-- common inputs -->
                                            <input id="SEO Title" type="text" class="form-control "
                                                placeholder="Enter seo title" min="" multiple="">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group px-3 ">
                                            <label class="form-label flex-grow-1" for="SEO Description">
                                                <strong>SEO Description</strong> <span class="text-danger">*</span>:
                                            </label>

                                            <!-- textarea input -->
                                            <textarea id="SEO Description" class="form-control" placeholder="SEO Description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group px-3">
                                            <label class="form-label flex-grow-1"
                                                for="genres"><strong>Keywords:</strong></label>
                                            <select id="genres" type="select"
                                                class="form-control select2-basic-multiple" placeholder="select genres"
                                                tabindex="0" aria-hidden="false">
                                                <option>A</option>
                                                <option>B</option>
                                                <option>C</option>
                                                <option>D</option>
                                                <option>E</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>Media</legend>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group px-3 ">
                                            <label class="form-label flex-grow-1" for="Thumbnail">
                                                <strong>Thumbnail</strong> <span class="text-danger">*</span>:
                                            </label>

                                            <!-- textarea input -->
                                            <!-- toggle switch -->
                                            <!-- common inputs -->
                                            <input id="Thumbnail" type="file" class="form-control " placeholder=""
                                                min="" multiple="">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group px-3 ">
                                            <label class="form-label flex-grow-1" for="poster">
                                                <strong>poster</strong> <span class="text-danger">*</span>:
                                            </label>

                                            <!-- textarea input -->
                                            <!-- toggle switch -->
                                            <!-- common inputs -->
                                            <input id="poster" type="file" class="form-control " placeholder=""
                                                min="" multiple="">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group px-3 ">
                                            <label class="form-label flex-grow-1" for="Trailer Url">
                                                <strong>Trailer Url</strong> <span class="text-danger">*</span>:
                                            </label>

                                            <!-- textarea input -->
                                            <!-- toggle switch -->
                                            <!-- common inputs -->
                                            <input id="Trailer Url" type="text" class="form-control "
                                                placeholder="Trailer Link" min="" multiple="">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center my-5 px-3">
                                    <h5>
                                        <strong>Video Quality</strong>
                                    </h5>
                                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal"
                                        data-bs-target="#video-modal">
                                        <i class="fa-solid fa-square-plus me-2"></i>Add Video
                                    </button>

                                    <div class="modal fade" id="video-modal" tabindex="-1" role="dialog"
                                        aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="video-modal-label">Add</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="" class="section-form">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group px-3 d-flex flex-column">
                                                                    <label class="form-label flex-grow-1"
                                                                        for="quality"><strong>Quality:</strong></label>
                                                                    <select id="quality" type="select"
                                                                        class="form-control select2-basic-multiple"
                                                                        placeholder="Select Quality" tabindex="0"
                                                                        aria-hidden="false">
                                                                        <option>480p</option>
                                                                        <option>720p</option>
                                                                        <option>1080p</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <div class="form-group px-3 ">
                                                                    <label class="form-label flex-grow-1" for="Video">
                                                                        <strong>Video</strong> :
                                                                    </label>

                                                                    <!-- textarea input -->
                                                                    <!-- toggle switch -->
                                                                    <!-- common inputs -->
                                                                    <input id="Video" type="file"
                                                                        class="form-control " placeholder=""
                                                                        min="" multiple="">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <div
                                                                    class="form-group px-3 d-flex justify-content-between">
                                                                    <label class="form-label flex-grow-1"
                                                                        for="Download Link">
                                                                        <strong>Download Link</strong> :
                                                                    </label>

                                                                    <!-- textarea input -->
                                                                    <!-- toggle switch -->
                                                                    <div class="form-check form-switch ms-2">
                                                                        <input id="Download Link" class="form-check-input"
                                                                            type="checkbox">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-3">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Quality</th>
                                                <th>Video URL</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center">
                                                <td>720P</td>
                                                <td>video_720.mp4</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <a aria-current="page" href="#" class="active text-success"
                                                            title="Edit">
                                                            <i class="fa-solid fa-pen mx-4"></i>
                                                        </a>
                                                        <a aria-current="page" href="#" class="active text-danger"
                                                            title="Delete">
                                                            <i class="fa-solid fa-trash me-4"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center my-5 px-3">
                                    <h5>
                                        <strong>Subtitles</strong>
                                    </h5>
                                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal"
                                        data-bs-target="#subtitle-modal">
                                        <i class="fa-solid fa-square-plus me-2"></i>Add Subtitle
                                    </button>

                                    <div class="modal fade" id="subtitle-modal" tabindex="-1" role="dialog"
                                        aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="subtitle-modal-label">Add</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="" class="section-form">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group px-3 d-flex flex-column">
                                                                    <label class="form-label flex-grow-1"
                                                                        for="subtitle"><strong>Subtitle:</strong></label>
                                                                    <select id="quality" type="select"
                                                                        class="form-control select2-basic-multiple"
                                                                        placeholder="Select Quality" tabindex="0"
                                                                        aria-hidden="false">
                                                                        <option>480p</option>
                                                                        <option>720p</option>
                                                                        <option>1080p</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <div class="form-group px-3 ">
                                                                    <label class="form-label flex-grow-1" for="File">
                                                                        <strong>File</strong> :
                                                                    </label>

                                                                    <!-- textarea input -->
                                                                    <!-- toggle switch -->
                                                                    <!-- common inputs -->
                                                                    <input id="File" type="file"
                                                                        class="form-control " placeholder=""
                                                                        min="" multiple="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-3">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Language</th>
                                                <th>URL</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center">
                                                <td>English</td>
                                                <td>English.txt</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <a aria-current="page" href="#" class="active text-success"
                                                            title="Edit">
                                                            <i class="fa-solid fa-pen mx-4"></i>
                                                        </a>
                                                        <a aria-current="page" href="#" class="active text-danger"
                                                            title="Delete">
                                                            <i class="fa-solid fa-trash me-4"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset>
                            <div class="offcanvas-footer border-top">
                                <div class="d-grid d-flex gap-3 p-3">
                                    <button type="submit" class="btn btn-primary d-block">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>Save
                                    </button>
                                    <button type="button" class="btn btn-outline-primary d-block"
                                        data-bs-dismiss="offcanvas" aria-label="Close">
                                        <i class="fa-solid fa-angles-left me-2"></i>Close
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
