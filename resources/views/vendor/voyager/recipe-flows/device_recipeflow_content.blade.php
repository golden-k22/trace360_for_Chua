@foreach($physicalDevices as $device)

    <div class="container-fluid">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <h3 style="margin-bottom: 20px;">{{ $device->name }}</h3>
        <div class="form-group col-lg-12">
            <div class="form-group col-lg-6">
                <label class="col-form-label" for="device-input-{{ $device->physicaldevice_id }}-cycle">Cycle Time </label>
                <input type="number" name="device-input-{{ $device->physicaldevice_id }}-cycle" class="form-control" id="device-input-{{ $device->physicaldevice_id }}-cycle" value="{{ $device->cycle_time }}">
            </div>
            <div class="form-group col-lg-6">
                <label class="col-form-label" for="device-input-{{ $device->physicaldevice_id }}-lapse">Lapse Time </label>
                <input type="number" name="device-input-{{ $device->physicaldevice_id }}-lapse" class="form-control" id="device-input-{{ $device->physicaldevice_id }}-lapse" value="{{ $device->lapse_time }}">
            </div>
        </div>
        <div class="form-group col-lg-12">
            <div class="form-group col-lg-6">
                <label class="col-form-label" for="device-input-{{ $device->physicaldevice_id }}-qty">Qty Per Ctn </label>
                <input type="number" name="device-input-{{ $device->physicaldevice_id }}-qty" class="form-control" id="device-input-{{ $device->physicaldevice_id }}-qty" value="{{ $device->qty_per_ctn }}">
            </div>
            <div class="form-group col-lg-6">
                <label class="col-form-label" for="device-input-{{ $device->physicaldevice_id }}-ctnx">Ctn x count </label>
                <input type="number" name="device-input-{{ $device->physicaldevice_id }}-ctnx" class="form-control" id="device-input-{{ $device->physicaldevice_id }}-ctnx" value="{{ $device->ctn_x_count }}">
            </div>
        </div>
        <div class="form-group col-lg-12">
            <div class="form-group col-lg-6">
                <label class="col-form-label" for="device-input-{{ $device->physicaldevice_id }}-ctny">Ctn y count </label>
                <input type="number" name="device-input-{{ $device->physicaldevice_id }}-ctny" class="form-control" id="device-input-{{ $device->physicaldevice_id }}-ctny" value="{{ $device->ctn_y_count }}">
            </div>
            <div class="form-group col-lg-6">
                <label class="col-form-label" for="device-input-{{ $device->physicaldevice_id }}-ctnz">Ctn z count </label>
                <input type="number" name="device-input-{{ $device->physicaldevice_id }}-ctnz" class="form-control" id="device-input-{{ $device->physicaldevice_id }}-ctnz" value="{{ $device->ctn_z_count }}">
            </div>
        </div>
        <div class="form-group col-lg-12">
            <div class="form-group col-lg-6">
                <label class="col-form-label" for="device-input-{{ $device->physicaldevice_id }}-pack-qty">Pack Qty </label>
                <input type="number" name="device-input-{{ $device->physicaldevice_id }}-pack-qty" class="form-control" id="device-input-{{ $device->physicaldevice_id }}-pack-qty" value="{{ $device->pack_qty }}">
            </div>
            <div class="form-group col-lg-6">
                <label class="col-form-label" for="device-input-{{ $device->physicaldevice_id }}-pack-mat">Pack Material </label>
                <input type="number" name="device-input-{{ $device->physicaldevice_id }}-pack-mat" class="form-control" id="device-input-{{ $device->physicaldevice_id }}-pack-mat" value="{{ $device->pack_material }}">
            </div>
        </div>

    </div>
@endforeach

