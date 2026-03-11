<form action="admin/combo/create" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="combo" tabindex="-1" aria-labelledby="combo_title" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="combo_title">Combo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="nameCreate">@lang('lang.name')</label>
                                    <input id="nameCreate" class="form-control" type="text" name="name" required autocomplete="off"
                                           placeholder="@lang('lang.type') @lang('lang.name')">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="priceCreate">@lang('lang.price')</label>
                                    <input id="priceCreate" class="form-control" type="number" name="price"
                                           placeholder="@lang('lang.type') @lang('lang.price')">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group file-uploader">
                                    <label for="imgCreate">@lang('lang.image')</label>
                                    <input id="imgCreate" type='file' name='Image' class="form-control image-combo">
                                    <img style="width: 150px" src="" class="img_combo d-none" alt="...">
                                </div>
                            </div>
                            <div class="col-12 food_group">
                                <span class="form-label">Foods</span>
                                <div class="input-group m-1">
                                    <span class="input-group-text text-black-50">@lang('lang.food'): </span>
                                    <select type='text' name='food[]' class="form-select" aria-label="food">
                                        @foreach($foods as $food)
                                            <option value="{{$food->id}}">{{$food->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-text text-black-50">@lang('lang.quantity'): </span>
                                    <input type="number" name="quantity[]" class="form-control" placeholder="quantity..." aria-label="quantity">
                                    <button type="button" class="btn btn-danger mb-0 delete_food"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                            <button type="button" class="btn m-1 btn-primary add_food">ADD FOOD</button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('lang.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('lang.save')</button>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
$('.image-combo').change(function(e){

    let reader = new FileReader();

    reader.onload = function(e){
        $('.img_combo').attr('src', e.target.result);
        $('.img_combo').removeClass('d-none');
    }

    reader.readAsDataURL(e.target.files[0]);

});
</script>

