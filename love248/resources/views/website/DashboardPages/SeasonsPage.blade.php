@extends('layouts.app', ['module_title' => 'Seasons'])
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
                    <button class="btn btn-primary " data-bs-toggle="offcanvas" data-bs-target="#season-offcanvas"
                        aria-controls="season-offcanvas">
                        <i class="fa-solid fa-plus me-2" style="color: #fafcff;"></i>
                        Add Season
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

                                @include('components.datatable.DataTable', [
                                    'name' => 'arrival 1999',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/03.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'day of darkness',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/04.jpg'),
                                    'quality' => '480/1080',
                                    'duration' => '2h 40m',
                                    'genres' => ['action', 'fight', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'don jon',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/0 5.jpg'),
                                    'quality' => '720/1080',
                                    'duration' => '3h',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'mega fun',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/06.jpg'),
                                    'quality' => '1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'my true friends',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/07.jpg'),
                                    'quality' => '1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'night mare',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/08.jpg'),
                                    'quality' => '480',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'portable',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/09.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'suffered',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/10.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'the witcher',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/3.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'troll hunter',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/02.jpg'),
                                    'quality' => '720',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
                                    'name' => 'troll hunter',
                                    'thumbnail' => asset('dashboard/images/movie-thumb/03.jpg'),
                                    'quality' => '480/720/1080',
                                    'duration' => '2h 21m',
                                    'genres' => ['action', 'fight', 'thriller', 'etc'],
                                    'date' => '2010',
                                    'subtitles' => 'english, hindi',
                                ])
                                @include('components.datatable.DataTable', [
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
            <div class="offcanvas offcanvas-end offcanvas-width-80" tabindex="-1" id="season-offcanvas"
                aria-labelledby="season-offcanvas-lable">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasRightLabel1">Show List</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>

                <div class="offcanvas-body">
                    <form action="" class="section-form">
                        <fieldset>
                            <legend>Seasons</legend>
                            <div class="form-group px-3">
                                <div class="form-group px-3 ">
                                    <label class="form-label flex-grow-1" for="Seasons">
                                        <strong>Seasons</strong> :
                                    </label>

                                    <!-- textarea input -->
                                    <!-- toggle switch -->
                                    <!-- common inputs -->
                                    <input id="Seasons" type="number" class="form-control " placeholder="1"
                                        min="1" multiple="" />
                                </div>
                                <div class="form-group px-3 ">
                                    <label class="form-label flex-grow-1" for="Description">
                                        <strong>Description</strong> :
                                    </label>

                                    <!-- textarea input -->
                                    <textarea id="Description" class="form-control" placeholder="Description"></textarea>
                                </div>
                                <label class="form-label flex-grow-1" for="show"><strong>Show:</strong></label>
                                <select id="show" type="select" class="form-control">
                                    <option>abc</option>
                                    <option>xyz</option>
                                    <option>mno</option>
                                    <option>stu</option>
                                </select>
                            </div>
                        </fieldset>
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
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
