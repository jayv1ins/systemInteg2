<form id="myForm" action="/sendTest" method="POST">
    @csrf
    <div class="quantity">
        <h6>Quantity :</h6>
        <!-- Input Order -->
        <div class="input-group">
            <div class="button minus">
                <button type="button" class="btn btn-primary btn-number" disabled="disabled" data-type="minus"
                    data-field="quant[1]">
                    <i class="ti-minus"></i>
                </button>
            </div>
            <input type="hidden" name="slug">
            <div class="col-12">
                <div class="form-group">
                    <label>Your slug<span>*</span></label>
                    <input type="text" name="slug" required="required">
                    <br>
                    <label>Your price<span>*</span></label>
                    <input type="text" name="price" required="required">
                    <br>
                    <label>Your amount<span>*</span></label>
                    <input type="text" name="amount" required="required">

                </div>
            </div>
            <input type="text" name="quant[1]" class="input-number" data-min="1" data-max="1000" value="1"
                id="quantity">
            <div class="button plus">
                <button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[1]">
                    <i class="ti-plus"></i>
                </button>
            </div>
        </div>
        <!--/ End Input Order -->
    </div>
    <div class="add-to-cart mt-4">
        <button type="submit" class="btn">Add to cart</button>
    </div>
</form>
