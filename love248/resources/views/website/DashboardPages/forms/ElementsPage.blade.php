@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-6 col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Basic Form</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="input-01" class="form-label">Email address:</label>
                            <input type="text" id="input-01" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="input-02" class="form-label">Password:</label>
                            <input type="password" id="input-02" class="form-control">
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-danger">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Form Grid</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="First Name">
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Last Name">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Input</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="input-101" class="form-label">Input Text</label>
                            <input type="text" id="input-101" class="form-control" placeholder="Mark Jhon">
                        </div>
                        <div class="form-group">
                            <label for="input-102" class="form-label">Email Input</label>
                            <input type="email" id="input-102" class="form-control" placeholder="markjhon@gmail.com">
                        </div>
                        <div class="form-group">
                            <label for="input-103" class="form-label">Url Input</label>
                            <input type="url" id="input-103" class="form-control"
                                placeholder="https://getbootstrap.com">
                        </div>
                        <div class="form-group">
                            <label for="input-104" class="form-label">Teliphone Input</label>
                            <input type="tel" id="input-104" class="form-control" placeholder="1-(555)-555-5555">
                        </div>
                        <div class="form-group">
                            <label for="input-105" class="form-label">Number Input</label>
                            <input type="number" id="input-105" class="form-control" placeholder="2356">
                        </div>
                        <div class="form-group">
                            <label for="input-106" class="form-label">Password Input</label>
                            <input type="password" id="input-106" class="form-control" value="markjhon123"
                                placeholder="markjhon123">
                        </div>
                        <div class="form-group">
                            <label for="input-107" class="form-label">Date Input</label>
                            <input type="date" id="input-107" class="form-control" placeholder="2019-12-18">
                        </div>
                        <div class="form-group">
                            <label for="input-108" class="form-label">Week Input</label>
                            <input type="week" id="input-108" class="form-control" placeholder="2019-W46">
                        </div>
                        <div class="form-group">
                            <label for="input-109" class="form-label">Time Input</label>
                            <input type="time" id="input-109" class="form-control" placeholder="13:45">
                        </div>
                        <div class="form-group">
                            <label for="input-110" class="form-label">Date and Time Input</label>
                            <input type="datetime-local" id="input-110" class="form-control"
                                placeholder="2019-12-19T13:45:00">
                        </div>
                        <div class="form-group">
                            <label for="input-111" class="form-label">Example textarea</label>
                            <textarea id="input-111" class="form-control" placeholder="Enter something..." rows="3" max-rows="6"></textarea>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-danger">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Input Size</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="input-201" class="form-label">Small</label>
                            <input type="text" id="input-201" class="form-control form-control-sm"
                                placeholder="size=sm">
                        </div>
                        <div class="form-group">
                            <label for="input-202" class="form-label">Default</label>
                            <input type="text" id="input-202" class="form-control" placeholder="Mark Jhon">
                        </div>
                        <div class="form-group">
                            <label for="input-203" class="form-label">Large</label>
                            <input type="text" id="input-203" class="form-control form-control-lg"
                                placeholder="size=lg">
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Select Size</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="input-201" class="form-label">Small</label>
                            <select id="input-201" class="form-select form-select-sm">
                                <!-- Options for the select element go here -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input-202" class="form-label">Default</label>
                            <select id="input-202" class="form-select">
                                <!-- Options for the select element go here -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input-203" class="form-label">Large</label>
                            <select id="input-203" class="form-select form-select-lg">
                                <!-- Options for the select element go here -->
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Horizontal Form</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <div class="row align-items-center">
                                <div class="col-sm-3">
                                    <label for="input-1101" class="form-label mb-0">Email:</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" id="input-1101" class="form-control"
                                        placeholder="Enter Your Email">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row align-items-center">
                                <div class="col-sm-3">
                                    <label for="input-1102" class="form-label mb-0">Password:</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="password" id="input-1102" class="form-control"
                                        placeholder="Enter Your Password">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-danger">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Form Row</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="First Name">
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Last Name">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Input</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label class="form-label">Disabled Input</label>
                            <input type="text" disabled class="form-control" placeholder="John Carter">
                        </div>
                    </form>
                    <div class="card-body">
                        <form>
                            <div class="form-group">
                                <div class="row align-items-center">
                                    <div class="col-sm-3">
                                        <label for="input-2201" class="form-label mb-0">Email:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" id="input-2201" class="form-control"
                                            placeholder="Enter Your Email">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row align-items-center">
                                    <div class="col-sm-3">
                                        <label for="input-2202" class="form-label mb-0">Password:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="password" id="input-2202" class="form-control"
                                            placeholder="Enter Your Password">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="rememberMe">
                                    <label class="form-check-label" for="rememberMe">Remember me</label>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="button" class="btn btn-danger">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Form Row</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="input-1301" class="form-label">Readonly</label>
                            <input id="input-1301" type="text" readonly disabled class="form-control"
                                placeholder="John Carter">
                        </div>
                        <div class="form-group">
                            <label for="input-1302" class="form-label">Input Color</label>
                            <input id="input-1302" type="color" class="form-control w-100">
                        </div>
                        <div class="form-group">
                            <label for="input-1303" class="form-label">Select input</label>
                            <select id="input-1303" class="form-select">
                                <!-- Options for the select element go here -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input-1304" class="form-label">Select Input New</label>
                            <select id="input-1304" class="form-select">
                                <!-- Options for the select element go here -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input-1305" class="form-label">Default</label>
                            <select id="input-1305" class="form-select" size="4">
                                <!-- Options for the select element go here -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input-1306" class="form-label">Example multiple select</label>
                            <select id="input-1306" class="form-select" multiple size="4">
                                <!-- Options for the select element go here -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input-1307" class="form-label">Example multiple select</label>
                            <input id="input-1307" type="range">
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="defaultCheckbox">
                                <label class="form-check-label" for="defaultCheckbox">Default Checkbox</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkedCheckbox" checked>
                                <label class="form-check-label" for="checkedCheckbox">Checked Checkbox</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="disabledCheckbox" disabled>
                                <label class="form-check-label" for="disabledCheckbox">Default Checkbox</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="disabledCheckedCheckbox" disabled
                                    checked>
                                <label class="form-check-label" for="disabledCheckedCheckbox">Checked Checkbox</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="defaultRadio">
                                <label class="form-check-label" for="defaultRadio">Default Radio</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="checkedRadio" checked>
                                <label class="form-check-label" for="checkedRadio">Checked Radio</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="disabledRadio" disabled>
                                <label class="form-check-label" for="disabledRadio">Default Radio</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="disabledCheckedRadio" disabled
                                    checked>
                                <label class="form-check-label" for="disabledCheckedRadio">Checked Radio</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="defaultSwitchCheckbox">
                                <label class="form-check-label" for="defaultSwitchCheckbox">Default switch checkbox
                                    input</label>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-danger">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
