<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Stream Stats</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css" />
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

</head>

<body class="antialiased">
    <div class="container-fluid">
        <div class="row m-3">
            <div class="col-3">
                <div class="card m-3">
                    <div class="card-body text-center">
                        <h5>Median Viewer Count</h5>
                        <h2 id="viewer-count-median"></h2>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card m-3">
                    <div class="card-body text-center" title="For the stream followed by you with lowest viewers">
                        <h5>Viewers Required To Top</h5>
                        <h2 id="viewer-required-to-top"></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m-3">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        Total Streams for each Game
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="streams-by-games-table" class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Game Name</th>
                                        <th scope="col">Streams</th>
                                    </tr>
                                </thead>
                                <tbody id="streams-by-games-tbody">
                                    <!-- Populated via AJAX -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        Total Games By Viewer Count
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="games-by-viewer-table" class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Game Name</th>
                                        <th scope="col">Viewer Count</th>
                                    </tr>
                                </thead>
                                <tbody id="games-by-viewer-tbody">
                                    <!-- Populated via AJAX -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row m-3">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        Total Streams By Viewer Count
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="streams-by-viewer-table" class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Stream</th>
                                        <th scope="col">Viewer Count</th>
                                    </tr>
                                </thead>
                                <tbody id="streams-by-viewer-tbody">
                                    <!-- Populated via AJAX -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        Total Streams Grouped By Start Time
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="streams-by-starttime-table" class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Start Time</th>
                                        <th scope="col">Streams</th>
                                    </tr>
                                </thead>
                                <tbody id="streams-by-starttime-tbody">
                                    <!-- Populated via AJAX -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row m-3">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        Top Streams Followed by You
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="streams-by-user-table" class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Stream</th>
                                    </tr>
                                </thead>
                                <tbody id="streams-by-user-tbody">
                                    <!-- Populated via AJAX -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        Tags Shared with Top Streams
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="streams-by-tags-table" class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Tags</th>
                                    </tr>
                                </thead>
                                <tbody id="streams-by-tags-tbody">
                                    <!-- Populated via AJAX -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>


    <script>
        $(document).ready(function() {
            loadData();
            setInterval(function() {
                window.location.reload();
            }, 1000000);
        });

        function loadData() {
            $.ajax({
                url: '/streams/getStreamsByGameName',
                type: 'GET',
                success: function(response) {
                    data = response.data;
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        html += '<tr>';
                        html += '<td>' + data[i].game_name + '</td>';
                        html += '<td>' + data[i].number_of_streams + '</td>';
                        html += '</tr>';
                    }
                    $('#streams-by-games-tbody').html(html);
                    $('#streams-by-games-table').DataTable({
                        destroy: true,
                        "order": [
                            [1, "desc"]
                        ],
                        "pageLength": 5
                    });
                }
            });

            $.ajax({
                url: '/streams/getTopGamesByViewerCount',
                type: 'GET',
                success: function(response) {
                    data = response.data;
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        html += '<tr>';
                        html += '<td>' + data[i].game_name + '</td>';
                        html += '<td>' + data[i].viewer_count + '</td>';
                        html += '</tr>';
                    }
                    $('#games-by-viewer-tbody').html(html);
                    $('#games-by-viewer-table').DataTable({
                        destroy: true,
                        "order": [
                            [1, "desc"]
                        ],
                        "pageLength": 5
                    });
                }
            });


            $.ajax({
                url: '/streams/getTopStreamsByViewerCount',
                type: 'GET',
                success: function(response) {
                    data = response.data;
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        html += '<tr>';
                        html += '<td>' + data[i].title + '</td>';
                        html += '<td>' + data[i].viewer_count + '</td>';
                        html += '</tr>';
                    }
                    $('#streams-by-viewer-tbody').html(html);
                    $('#streams-by-viewer-table').DataTable({
                        destroy: true,
                        "order": [
                            [1, "desc"]
                        ],
                        "pageLength": 5
                    });
                }
            });

            $.ajax({
                url: '/streams/getTopStreamsFollowedByUser',
                type: 'GET',
                success: function(response) {
                    data = response.data;
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        html += '<tr>';
                        html += '<td>' + data[i].title + '</td>';
                        html += '</tr>';
                    }
                    $('#streams-by-user-tbody').html(html);
                    $('#streams-by-user-table').DataTable({
                        destroy: true,
                        "order": [
                            [0, "desc"]
                        ],
                        "pageLength": 5
                    });
                }
            });


            $.ajax({
                url: '/streams/getStreamsGroupedByStartTime',
                type: 'GET',
                success: function(response) {
                    data = response.data;
                    var html = '';

                    $.each(data, function(key, value) {
                        html += '<tr>';
                        html += '<td>' + key + ':00</td>';
                        html += '<td>' + value.length + '</td>';
                        html += '</tr>';
                    });

                    $('#streams-by-starttime-tbody').html(html);
                    $('#streams-by-starttime-table').DataTable({
                        destroy: true,
                        "order": [
                            [1, "desc"]
                        ],
                        "pageLength": 5
                    });
                }
            });

            $.ajax({
                url: '/streams/getTopStreamsUserSharedTags',
                type: 'GET',
                success: function(response) {
                    data = response.data;
                    var html = '';

                    $.each(data, function(key, value) {
                        html += '<tr>';
                        html += '<td>' + value.tag_name + '</td>';
                        html += '</tr>';
                    });

                    $('#streams-by-tags-tbody').html(html);
                    $('#streams-by-tags-table').DataTable({
                        destroy: true,
                        "order": [
                            [0, "desc"]
                        ],
                        "pageLength": 5
                    });
                }
            });

            $.ajax({
                url: '/streams/getViewerCountMedian',
                type: 'GET',
                success: function(response) {
                    data = response.data;
                    $('#viewer-count-median').html(data);
                }
            });

            $.ajax({
                url: '/streams/getViewersRequiredToReachTop',
                type: 'GET',
                success: function(response) {
                    data = response.data;
                    $('#viewer-required-to-top').html(data);
                }
            });
        }
    </script>
</body>

</html>
