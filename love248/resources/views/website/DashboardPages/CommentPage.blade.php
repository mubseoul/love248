@extends('layouts.app', ['module_title' => 'Comment Lists'])

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center pb-3">
                    <div class="card-title">
                        <h4>Comment List</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-view table-responsive table-space">
                        <table id="commentTable" class="data-tables table custom-table movie_table" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Description</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('components.widget.comment-lists', [
                                    'commentNo' => '1',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Nick Allen',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '21 Jul, 2020',
                                ])

                                @include('components.widget.comment-lists', [
                                    'commentNo' => '2',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Hans Olo',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '12 Jun, 2020',
                                ])

                                @include('components.widget.comment-lists', [
                                    'commentNo' => '3',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Lynn Guini',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '01 Jun, 2020',
                                ])


                                @include('components.widget.comment-lists', [
                                    'commentNo' => '3',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Aaronottix',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '19 Mar, 2020',
                                ])


                                @include('components.widget.comment-lists', [
                                    'commentNo' => '3',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Marge Arita',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '21 Aug, 2020',
                                ])

                                @include('components.widget.comment-lists', [
                                    'commentNo' => '3',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Bill Dabear',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '15 May, 2020',
                                ])

                                @include('components.widget.comment-lists', [
                                    'commentNo' => '3',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Brock Lee',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '07 Jul, 2020',
                                ])


                                @include('components.widget.comment-lists', [
                                    'commentNo' => '3',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Nick Allen',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '21 Jul, 2020',
                                ])

                                @include('components.widget.comment-lists', [
                                    'commentNo' => '3',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Hans Olo',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '12 Jun, 2020',
                                ])

                                @include('components.widget.comment-lists', [
                                    'commentNo' => '3',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Lynn Guini',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '01 Jan, 2020',
                                ])

                                @include('components.widget.comment-lists', [
                                    'commentNo' => '3',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Aaronottix',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '19 Mar, 2020, 2020',
                                ])

                                @include('components.widget.comment-lists', [
                                    'commentNo' => '3',
                                    'commentTitle' => 'Lorem ipsum dolor',
                                    'commentAuth' => 'Marge Arita',
                                    'commentText' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed rhoncus non elit a
                                                                                        scelerisque. Etiam feugiat
                                                                                        luctus est, vel commodo odio rhoncus sit amet",
                                    'commentDate' => '21 Aug, 2020, 2020',
                                ])
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection
