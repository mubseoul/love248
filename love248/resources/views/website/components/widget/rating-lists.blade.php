<tr>
    <td>{{$ratingNo}}</td>
    <td>
        {{$ratingTitle}}
    </td>
    <td>
        <p class="mb-0"> {{$ratingName}}</p>
    </td>
    <td>
        <p class="mb-0">{{$ratingText}}</p>
    </td>
    <td>
        {{$ratingYear}}
    </td>
    <td><i className="fa-solid fa-star text-primary"></i> {{$ratingStar}}</td>
    <td>
        <div class="d-flex align-items-center list-user-action">
            <a class="btn btn-sm btn-icon btn-warning rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="View" href="#">
                <i class="fa-solid fa-eye"></i>
            </a>
            <a class="btn btn-sm btn-icon btn-success rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" href="#">
                <i class="fa-solid fa-pen"></i>
            </a>
            <a class="btn btn-sm btn-icon btn-danger rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" href="#">
                <i class="fa-solid fa-trash"></i>
            </a>
        </div>
    </td>
</tr>
