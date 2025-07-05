@extends('layouts.app', ['module_title' => 'Show List'])
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center pb-3">
                    <div class="d-flex align-items-center">
                        <div class="form-group">
                            <select type="select" class="form-control select2-basic-multiple" placeholder="No Action">
                                <option>No Action</option>
                                <option>Status</option>
                                <option>Delete</option>
                            </select>
                            <button class="btn btn-secondary h-50 disabled">Apply</button>
                        </div>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#season-offcanvas"
                        aria-controls="season-offcanvas">
                        <i class="fa-solid fa-plus me-2" style="color: #fafcff;"></i>
                        Add Show
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-view table-responsive pt-3">
                        <table id="movieTable" class="data-tables table movie_table" data-toggle="data-table">
                            <thead>
                                <tr class="text-uppercase">
                                    <th>
                                        <input type="checkbox" class="form-check-input" />
                                    </th>
                                    <th>Show</th>
                                    <th>Quality</th>
                                    <th>Category</th>
                                    <th>Publish Date</th>
                                    <th>Show Access</th>
                                    <th>Seo</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('components/datatable/DataTable', [
                                    'name' => 'arrival 1999',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/03.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components/datatable/DataTable', [
                                    'name' => 'day of darkness',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/04.jpg'),
                                    'quality' => '480/1080',
                                    'duration' => '2h 40m',
                                    'genres' => ['action', 'fight', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components/datatable/DataTable', [
                                    'name' => 'don jon',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/05.jpg'),
                                    'quality' => '720/1080',
                                    'duration' => '3h',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components/datatable/DataTable', [
                                    'name' => 'mega fun',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/06.jpg'),
                                    'quality' => '1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components/datatable/DataTable', [
                                    'name' => 'my true friends',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/07.jpg'),
                                    'quality' => '1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components/datatable/DataTable', [
                                    'name' => 'night mare',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/08.jpg'),
                                    'quality' => '480',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components/datatable/DataTable', [
                                    'name' => 'portable',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/09.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components/datatable/DataTable', [
                                    'name' => 'suffered',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/10.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components/datatable/DataTable', [
                                    'name' => 'the witcher',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/03.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components/datatable/DataTable', [
                                    'name' => 'troll hunter',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/02.jpg'),
                                    'quality' => '720',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components/datatable/DataTable', [
                                    'name' => 'troll hunter',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/03.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components/datatable/DataTable', [
                                    'name' => 'troll hunter',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/04.jpg'),
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
                    <h5 id="offcanvasRightLabel1">Show List</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <form>
                        <div class="section-form">
                            <fieldset>
                                <legend>Show</legend>

                                <div class="row mt-3">
                                    <div class="col-sm-12">
                                        <div class="form-group px-3 ">
                                            <label class="form-label flex-grow-1" for="Show Name">
                                                <strong>Show Name</strong> <span class="text-danger">*</span>:
                                            </label>

                                            <!-- textarea input -->
                                            <!-- toggle switch -->
                                            <!-- common inputs -->
                                            <input id="Show Name" type="text" class="form-control "
                                                placeholder="Enter Show Name" value="" min="" multiple="">
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
                                </div>
                                <div class="row mt-3 px-3">
                                    <div class="col-lg-6">
                                        <label class="form-label flex-grow-1" for="show"><strong>Show
                                                Access:</strong></label>
                                        <div class="form-group px-3">
                                            <select id="show" type="select" class="select2 form-control">
                                                <option>Free</option>
                                                <option>Standard</option>
                                                <option>Premium</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label flex-grow-1"
                                            for="show"><strong>Language:</strong></label>
                                        <div class="form-group px-3">
                                            <select id="show" type="select" class="form-control">
                                                <option>English</option>
                                                <option>Hindi</option>
                                                <option>French</option>
                                                <option>Marathi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label class="form-label flex-grow-1"
                                            for="show"><strong>genres:</strong></label>
                                        <div class="form-group px-3">
                                            <select id="show" type="select" class="form-control">
                                                <option>Action</option>
                                                <option>Adventure</option>
                                                <option>Comedy</option>
                                                <option>Animation</option>
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
                                                                    value="" min="" multiple="">
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
                                                                    value="" min="" multiple="">
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
                                                placeholder="Rating" value="" min="" multiple="">
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
                                                placeholder="Duration in mins" value="" min=""
                                                multiple="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-4 px-3">
                                    <div class="form-group px-3 d-flex align-self-start justify-content-between"
                                        name="status">
                                        <label class="form-label flex-grow-1" for="Status"><strong>Status</strong>
                                            <!--v-if-->:</label><!-- textarea input --><!-- toggle switch -->
                                        <div class="d-flex justify-content-between">
                                            <div class="form-check form-switch ms-2"><input id="Status"
                                                    class="form-check-input" type="checkbox"></div>
                                        </div><span class="text-danger"></span>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </form>

                </div>
                <div class="offcanvas-footer border-top">
                    <div class="d-grid d-flex gap-3 p-3">
                        <button type="submit" class="btn btn-primary d-block">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Save
                        </button>
                        <button type="button" class="btn btn-outline-primary d-block" data-bs-dismiss="offcanvas"
                            aria-label="Close">
                            <i class="fa-solid fa-angles-left me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
