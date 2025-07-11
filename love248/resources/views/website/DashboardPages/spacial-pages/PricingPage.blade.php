@extends('layouts.app')

@section('title')
    {{ 'Dashboard' }}
@endsection

@section('content')
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none">
        <symbol id="check" viewBox="0 0 16 16">
            <title>Check</title>
            <path
                d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z">
            </path>
        </symbol>
    </svg>
    <div class="row">
        <div class="col-md-12">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 mb-3 text-center">
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header bg-soft-primary">
                            <h4 class="card-title pricing-card-title mb-3">Free</h4>
                            <h1 class="mb-3">
                                <b>$20</b><br />
                                /Month
                            </h1>
                            <button type="button" class="btn btn-primary mb-5">Get started</button>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled p-0 mb-0">
                                <li>
                                    <p>10 users included</p>
                                </li>
                                <li>
                                    <p>2 GB of storage</p>
                                </li>
                                <li>
                                    <p>Email support</p>
                                </li>
                                <li>
                                    <p class="mb-0">Help center access</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header bg-soft-primary">
                            <h4 class="card-title pricing-card-title mb-3">Pro</h4>
                            <h1 class="mb-3">
                                <b>$199</b><br />
                                /Month
                            </h1>
                            <button type="button" class="btn btn-primary mb-5">Get started</button>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled p-0 mb-0">
                                <li>
                                    <p>20 users included</p>
                                </li>
                                <li>
                                    <p>10GB of storage</p>
                                </li>
                                <li>
                                    <p>Priority Email support</p>
                                </li>
                                <li>
                                    <p class="mb-0">Help center access</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header bg-soft-primary">
                            <h4 class="card-title pricing-card-title mb-3">Enterprise</h4>
                            <h1 class="mb-3">
                                <b>$399</b><br />
                                /Month
                            </h1>
                            <button type="button" class="btn btn-primary mb-5">Get started</button>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 p-0">
                                <li>
                                    <p>30 users included</p>
                                </li>
                                <li>
                                    <p>15 GB of storage</p>
                                </li>
                                <li>
                                    <p>Call and email support</p>
                                </li>
                                <li>
                                    <p class="mb-0">Help center access</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header bg-soft-primary">
                            <h4 class="my-0 fw-normal mt-3">Premium</h4>
                            <h1 class="mb-3">
                                <b>$599</b><br />
                                /Month
                            </h1>
                            <button type="button" class="btn btn-primary mb-5">Get started</button>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 p-0">
                                <li>
                                    <p>50 users included</p>
                                </li>
                                <li>
                                    <p>60 GB of storage</p>
                                </li>
                                <li>
                                    <p>24 X 7 call support</p>
                                </li>
                                <li>
                                    <p class="mb-0">Help center access</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-cols-1">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header pb-3">
                            <h3 class="block-title">Pricing 1</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive pricing pt-2">
                                <table id="my-table" class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="prc-wrap" style="width: 20%"></th>
                                            <th class="prc-wrap" style="width: 20%">
                                                <div class="pricing-box">
                                                    <div class="h4">Free</div>
                                                    <div class="h2"><b>$0</b><small> / month</small></div>
                                                    <small class="type-1">Recommended</small>
                                                </div>
                                            </th>
                                            <th class="prc-wrap" style="width: 20%">
                                                <div class="pricing-box active">
                                                    <div class="h4">Pro</div>
                                                    <div class="h2"><b>$15</b><small> / month</small></div>
                                                    <small class="type-1">Recommended</small>
                                                </div>
                                            </th>
                                            <th class="prc-wrap" style="width: 20%">
                                                <div class="pricing-box">
                                                    <div class="h4">Enterprise</div>
                                                    <div class="h2"><b>$29</b><small> / month</small></div>
                                                    <small class="type-1">Recommended</small>
                                                </div>
                                            </th>
                                            <th class="prc-wrap" style="width: 20%">
                                                <div class="pricing-box">
                                                    <div class="h4">Premium</div>
                                                    <div class="h2"><b>$49</b><small> / month</small></div>
                                                    <small class="type-1">Recommended</small>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">Features 1</th>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell active">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Features 2</th>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4 20L20 4M20 20L4 4" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell active">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Features 3</th>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4 20L20 4M20 20L4 4" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell active">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Features 4</th>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell active">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Features 5</th>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4 20L20 4M20 20L4 4" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell active">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4 20L20 4M20 20L4 4" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                            <td class="text-center child-cell">
                                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23 7L6.44526 17.8042C5.85082 18.1921 5.0648 17.9848 4.72059 17.3493L1 10.4798"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"></td>
                                            <td class="text-center"><a href="#"
                                                    class="btn btn-outline-primary">Free</a></td>
                                            <td class="text-center"><a href="#"
                                                    class="btn btn-outline-primary">Purchase</a></td>
                                            <td class="text-center"><a href="#"
                                                    class="btn btn-outline-primary">Purchase</a></td>
                                            <td class="text-center"><a href="#"
                                                    class="btn btn-outline-primary">Purchase</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 mb-3 text-center">
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header bg-soft-primary p-4">
                            <h3 class="card-title pricing-card-title mb-3"><b>Basic Plan</b></h3>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled p-0 mb-0">
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>
                                        Sofbox series
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>
                                        Streamit Special
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-cente">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>Prokit HD Shows
                                    </p>
                                </li>
                            </ul>
                            <h4 class="mb-5"><b>$9 </b>/Month</h4>
                            <button type="button" class="btn btn-primary">Active</button>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header bg-soft-primary p-4">
                            <h3 class="card-title pricing-card-title mb-3"><b>Standard Plan</b></h3>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled p-0 mb-0">
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>
                                        Sofbox series
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>
                                        Streamit Special
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-cente">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>Prokit HD Shows
                                    </p>
                                </li>
                            </ul>
                            <h4 class="mb-5"><b>$29 </b>/Month</h4>
                            <button type="button" class="btn btn-primary">Active</button>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header bg-soft-primary p-4">
                            <h3 class="card-title pricing-card-title mb-3"><b>Professional Plan</b></h3>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled p-0 mb-0">
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>
                                        Sofbox series
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>Streamit Special
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-cente">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>Prokit HD Shows
                                    </p>
                                </li>
                            </ul>
                            <h4 class="mb-5"><b>$49 </b>/Month</h4>
                            <button type="button" class="btn btn-primary">Active</button>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header bg-soft-primary p-4">
                            <h3 class="card-title pricing-card-title mb-3"><b>Business Plan</b></h3>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled p-0 mb-0">
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>
                                        Sofbox series
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>Streamit Special
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-cente">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>Prokit HD Shows
                                    </p>
                                </li>
                            </ul>
                            <h4 class="mb-5"><b>$39 </b>/Month</h4>
                            <button type="button" class="btn btn-primary">Active</button>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header bg-soft-primary p-4">
                            <h3 class="card-title pricing-card-title mb-3"><b>VIP Plan</b></h3>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled p-0 mb-0">
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>
                                        Sofbox series
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>Streamit Special
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-cente">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>Prokit HD Shows
                                    </p>
                                </li>
                            </ul>
                            <h4 class="mb-5"><b>$69 </b>/Month</h4>
                            <button type="button" class="btn btn-primary">Active</button>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header bg-soft-primary p-4">
                            <h3 class="card-title pricing-card-title mb-3"><b>Premium Plan</b></h3>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" class="me-2" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                    fill="currentColor" />
                                <path
                                    d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled p-0 mb-0">
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>
                                        Sofbox series
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-center">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>Streamit Special
                                    </p>
                                </li>
                                <li class="mb-5">
                                    <p class="mb-0 d-flex justify-content-center align-items-cente">
                                        <svg width="14" height="14" class="me-3" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.65103 1.07509L9.13535 4.05794C9.24471 4.27405 9.45342 4.42413 9.69414 4.45748L13.0282 4.94306C13.2229 4.97041 13.3996 5.07312 13.519 5.22987C13.637 5.38462 13.6877 5.58072 13.659 5.77348C13.6356 5.93356 13.5603 6.08164 13.4449 6.19503L11.0291 8.53689C10.8524 8.7003 10.7724 8.94242 10.815 9.17921L11.4098 12.4716C11.4732 12.8691 11.2098 13.2439 10.815 13.3193C10.6523 13.3453 10.4856 13.318 10.3389 13.2433L7.36497 11.6938C7.14426 11.5824 6.88353 11.5824 6.66282 11.6938L3.68885 13.2433C3.32343 13.4374 2.87067 13.3053 2.66729 12.9451C2.59194 12.8017 2.56527 12.6383 2.58994 12.4789L3.18474 9.18588C3.22741 8.94976 3.14673 8.7063 2.97069 8.54289L0.554841 6.20236C0.267446 5.92489 0.258777 5.46799 0.535503 5.18051C0.541504 5.17451 0.548172 5.16784 0.554841 5.16117C0.669532 5.04444 0.820231 4.97041 0.982932 4.95106L4.31698 4.46481C4.55703 4.4308 4.76574 4.28206 4.87577 4.06461L6.30674 1.07509C6.4341 0.818961 6.69816 0.659547 6.98489 0.666217H7.07424C7.32296 0.696232 7.53967 0.850311 7.65103 1.07509Z"
                                                fill="currentColor" />
                                            <path
                                                d="M6.99484 11.6108C6.8657 11.6148 6.7399 11.6495 6.62674 11.7115L3.66731 13.2574C3.3052 13.4303 2.87187 13.2961 2.66885 12.9499C2.59363 12.8084 2.56634 12.6463 2.59164 12.4875L3.18272 9.20145C3.22266 8.96259 3.14279 8.71972 2.96905 8.55158L0.552121 6.21167C0.265231 5.93077 0.259906 5.46972 0.540805 5.18216C0.544799 5.17815 0.548127 5.17482 0.552121 5.17148C0.666611 5.05805 0.814383 4.98333 0.97347 4.95997L4.31032 4.4689C4.55194 4.43821 4.76162 4.28742 4.86812 4.06858L6.31855 1.04143C6.45634 0.797228 6.72059 0.651775 7.00016 0.667121C6.99484 0.865283 6.99484 11.476 6.99484 11.6108Z"
                                                fill="currentColor" />
                                        </svg>Prokit HD Shows
                                    </p>
                                </li>
                            </ul>
                            <h4 class="mb-5"><b>$69 </b>/Month</h4>
                            <button type="button" class="btn btn-primary">Active</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="justify-content-center justify-content-sm-between d-flex card-header border-bottom">
                    <h4 class="d-inline-block">Choose Plan</h4>
                    <div class="d-flex justify-content-center align-items-center mt-1 mt-sm-0 col-sm-auto">
                        <label class="me-2 form-check-label">Monthly</label>
                        <div class="form-check form-switch">
                            <input id="contcheckbox" type="checkbox" class="form-check-input" />
                            <label for="yearly" class="ms-2 align-top form-check-label">Yearly</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
                        <div class="col">
                            <div class="card pricing-box-2 mb-lg-0">
                                <div class="card-body">
                                    <h3>Premium</h3>
                                    <p>Best For Everyone</p>
                                    <h2 class="my-4 pricingtable__highlight montlypricing my-4"><b>$50/Mo</b></h2>
                                    <h2 class="my-4 pricingtable__highlight yearlypricing my-4"><b>$150/Mo</b></h2>
                                    <ul class="p-0 mb-0 list-unstyled">
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Unlimited Library Access
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Personalized for you
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>How and when you want
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Interactive learning
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Certificate of completion
                                            </p>
                                        </li>
                                    </ul>
                                    <div class="type-2">Popular</div>
                                    <button type="button" class="btn btn-primary border">Try For Free</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card pricing-box-2 active mb-lg-0">
                                <div class="card-body">
                                    <h3>Pro</h3>
                                    <p>Best For Professionals</p>
                                    <h2 class="my-4 pricingtable__highlight montlypricing my-4"><b>$100/Mo</b></h2>
                                    <h2 class="my-4 pricingtable__highlight yearlypricing my-4"><b>$200/Mo</b></h2>
                                    <ul class="list-unstyled p-0 mb-0">
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Unlimited Library Access
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Personalized for you
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>How and when you want
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Interactive learning
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Certificate of completion
                                            </p>
                                        </li>
                                    </ul>
                                    <div class="type-2">Popular</div>
                                    <button type="button" class="btn btn-primary border">Try For Free</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card pricing-box-2 mb-lg-0">
                                <div class="card-body">
                                    <h3>Starter</h3>
                                    <p>Best For Beginners</p>
                                    <h2 class="my-4 pricingtable__highlight montlypricing my-4"><b>$25/Mo</b></h2>
                                    <h2 class="my-4 pricingtable__highlight yearlypricing my-4"><b>$50/Mo</b></h2>
                                    <ul class="list-unstyled p-0 mb-0">
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Unlimited Library Access
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Personalized for you
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>How and when you want
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Interactive learning
                                            </p>
                                        </li>
                                        <li>
                                            <p class="mb-4">
                                                <svg width="8" class="me-2" viewBox="0 0 8 8" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                                                </svg>Certificate of completion
                                            </p>
                                        </li>
                                    </ul>
                                    <div class="type-2">Popular</div>
                                    <button type="button" class="btn btn-primary border">Try For Free</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
