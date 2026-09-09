<div id="whatsappmessagesettings" class="hidechild">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 box-shadow">
                <div class="card-header bg-secondary rounded-top-4 py-3 d-flex align-items-center text-white">
                    <i class="fa-brands fa-whatsapp fs-5"></i>
                    <h5 class="px-2">{{ trans('labels.whatsapp_message_settings') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ URL::to('admin/settings/order_message_update') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ trans('labels.whatsapp_number') }}<span
                                            class="text-danger"> *
                                        </span></label>
                                    <input type="text" class="form-control" name="whatsapp_number"
                                        value="{{ @whatsapp_helper::whatsapp_message_config($vendor_id)->whatsapp_number }}"
                                        placeholder="{{ trans('labels.whatsapp_number') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="form-label" for="">{{ trans('labels.whatsapp_chat') }}
                                </label>
                                <div class="text-center">
                                    <input id="whatsapp_chat_on_off" type="checkbox" class="checkbox-switch"
                                        name="whatsapp_chat_on_off" value="1"
                                        {{ @whatsapp_helper::whatsapp_message_config($vendor_id)->whatsapp_chat_on_off == 1 ? 'checked' : '' }}>
                                    <label for="whatsapp_chat_on_off" class="switch">
                                        <span
                                            class="{{ session()->get('direction') == 2 ? 'switch__circle-rtl' : 'switch__circle' }}"><span
                                                class="switch__circle-inner"></span></span>
                                        <span
                                            class="switch__left {{ session()->get('direction') == 2 ? 'pe-2' : 'ps-2' }}">{{ trans('labels.off') }}</span>
                                        <span
                                            class="switch__right {{ session()->get('direction') == 2 ? 'ps-2' : 'pe-2' }}">{{ trans('labels.on') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="form-label" for="">{{ trans('labels.mobile_view_display') }}
                                </label>
                                <div class="text-center">
                                    <input id="whatsapp_mobile_view_on_off" type="checkbox" class="checkbox-switch"
                                        name="whatsapp_mobile_view_on_off" value="1"
                                        {{ @whatsapp_helper::whatsapp_message_config($vendor_id)->whatsapp_mobile_view_on_off == 1 ? 'checked' : '' }}>
                                    <label for="whatsapp_mobile_view_on_off" class="switch">
                                        <span
                                            class="{{ session()->get('direction') == 2 ? 'switch__circle-rtl' : 'switch__circle' }}"><span
                                                class="switch__circle-inner"></span></span>
                                        <span
                                            class="switch__left {{ session()->get('direction') == 2 ? 'pe-2' : 'ps-2' }}">{{ trans('labels.off') }}</span>
                                        <span
                                            class="switch__right {{ session()->get('direction') == 2 ? 'ps-2' : 'pe-2' }}">{{ trans('labels.on') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <p class="form-label">
                                    {{ trans('labels.whatsapp_chat_position') }}
                                </p>
                                <div class="form-check form-check-inline m-0 p-0">
                                    <div class="d-flex gap-2 align-items-center">
                                        <input class="form-check-input form-check-input-secondary m-0 p-0"
                                            type="radio" name="whatsapp_chat_position" id="chatradio" value="1"
                                            {{ @whatsapp_helper::whatsapp_message_config($vendor_id)->whatsapp_chat_position == '1' ? 'checked' : '' }} />
                                        <label for="chatradio"
                                            class="form-check-label">{{ trans('labels.left') }}</label>
                                    </div>
                                </div>
                                <div class="form-check form-check-inline m-0 p-0">
                                    <div class="d-flex gap-2 align-items-center">
                                        <input class="form-check-input form-check-input-secondary m-0 p-0"
                                            type="radio" name="whatsapp_chat_position" id="chatradio1" value="2"
                                            {{ @whatsapp_helper::whatsapp_message_config($vendor_id)->whatsapp_chat_position == '2' ? 'checked' : '' }} />
                                        <label for="chatradio1"
                                            class="form-check-label">{{ trans('labels.right') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                <button
                                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                    @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
