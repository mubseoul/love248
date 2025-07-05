<tr>
    <td class="text-center">
        <input type="checkbox" class="form-check-input" />
    </td>
    <td>
        <div class="d-flex">
          <img src="{{  $thumbnail }}" alt="image" class="rounded-2 avatar-55 img-fluid" loading="lazy" />
          <div class="d-flex flex-column ms-3 justify-content-center">
            <h6 class="text-capitalize">{{ $name}}</h6>
            <span>{{ $duration}}</span>
            <span class="text-capitalize">({{ $subtitles}})</span>
          </div>
        </div>
    </td>
    <td class="text-center">
        <span>{{ $quality}} </span>
      </td>
      <td>
          @foreach($genres as $genre)
        <span class="text-capitalize"> {{ $genre}}, </span>
        @endforeach
    </td>
      <td class="text-center">
        <span>{{ $date}} </span>
      </td>
      <td>World</td>
      <td>!!!</td>
      <td class="text-center">
        <div class="d-flex justify-content-between">
          <div class="form-check form-switch ms-2">
            <input class="form-check-input" type="checkbox" />
          </div>
        </div>
      </td>
      <td>
        <div class="d-flex gap-2 align-items-center">
          <a class="btn btn-sm  btn-success rounded" href="#"><i class="fa-solid fa-pen"></i></a>
          <a class="btn btn-sm  btn-danger rounded" href="#"><i class="fa-solid fa-trash"></i></a>
        </div>
      </td>
</tr>
