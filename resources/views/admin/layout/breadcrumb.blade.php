<nav aria-label="breadcrumb">

    <ol class="breadcrumb breadcrumb-rtl">

        <li class="breadcrumb-item"><a href="{{ URL::to('admin/dashboard') }}">{{ trans('labels.dashboard') }}</a></li>

        @if (request()->is('admin/transaction'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.transaction') }}</li>
        @endif
        @if (request()->is('admin/transaction/plandetails-*'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/transaction') }}">{{ trans('labels.transaction') }}</a>
            </li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">
                {{ trans('labels.plan_details') }}

            </li>
        @endif

        @if (request()->is('admin/report*'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.report') }}</li>
        @endif

        @if (request()->is('admin/reviews'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.product_reviews') }}</li>
        @endif

        @if (request()->is('admin/orders'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.orders') }}</li>
        @endif
        @if (request()->is('admin/top_deals'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.top_deals') }}</li>
        @endif


        @if (request()->is('admin/orders/invoice*'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/orders') }}">{{ trans('labels.orders') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">Order details</li>
        @endif



        @if (request()->is('admin/plan'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.pricing_plan') }}</li>
        @endif



        @if (request()->is('admin/plan/add*') || request()->is('admin/plan/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/plan') }}">{{ trans('labels.pricing_plan') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/plan/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif

            </li>

        @endif





        @if (request()->is('admin/tableqr'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.tableqr') }}</li>
        @endif



        @if (request()->is('admin/tableqr/add*') || request()->is('admin/tableqr/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/tableqr') }}">{{ trans('labels.tableqr') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/tableqr/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif



            </li>

        @endif
        @if (request()->is('admin/employees'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.employee') }}</li>
        @endif



        @if (request()->is('admin/employees/add*') || request()->is('admin/employees/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/employees') }}">{{ trans('labels.employee') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/employees/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif

            </li>

        @endif

        @if (request()->is('admin/store_categories'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.store_categories') }}</li>
        @endif



        @if (request()->is('admin/store_categories/add*') || request()->is('admin/store_categories/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a
                    href="{{ URL::to('admin/store_categories') }}">{{ trans('labels.store_categories') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/store_categories/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif



            </li>

        @endif

        @if (request()->is('admin/tax'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.tax') }}</li>
        @endif



        @if (request()->is('admin/tax/add*') || request()->is('admin/tax/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/tax') }}">{{ trans('labels.tax') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/tax/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif



            </li>

        @endif

        @if (request()->is('admin/question_answer'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.question_answer') }}</li>
        @endif
        @if (request()->is('admin/currencys'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.currency') }}</li>
        @endif

        @if (request()->is('admin/currencys/currency_add*') || request()->is('admin/currencys/currency_edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/currencys') }}">{{ trans('labels.currency') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/currencys/currency_edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif
            </li>
        @endif

        @if (request()->is('admin/currency-settings'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.currency-settings') }}</li>
        @endif
        @if (request()->is('admin/currency-settings/add*') || request()->is('admin/currency-settings/currency/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/currency-settings') }}">{{ trans('labels.currency-settings') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/currency-settings/currency/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif
            </li>
        @endif

        @if (request()->is('admin/extras'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.global_extras') }}</li>
        @endif



        @if (request()->is('admin/extras/add*') || request()->is('admin/extras/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/extras') }}">{{ trans('labels.extras') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/extras/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif



            </li>

        @endif
        @if (request()->is('admin/custom_status'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.custom_status') }}</li>
        @endif



        @if (request()->is('admin/custom_status/add*') || request()->is('admin/custom_status/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a
                    href="{{ URL::to('admin/custom_status') }}">{{ trans('labels.custom_status') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/custom_status/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif



            </li>

        @endif



        @if (request()->is('admin/roles'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.roles') }}</li>
        @endif



        @if (request()->is('admin/roles/add*') || request()->is('admin/roles/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/roles') }}">{{ trans('labels.roles') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/roles/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif



            </li>

        @endif
        @if (request()->is('admin/customers'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.customers') }}</li>
        @endif



        @if (request()->is('admin/customers/add*') || request()->is('admin/customers/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/customers') }}">{{ trans('labels.customers') }}</a>
            </li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/customers/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif



            </li>

        @endif
        @if (request()->is('admin/notification'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.firebase_notification') }}</li>
        @endif



        @if (request()->is('admin/notification/add*') || request()->is('admin/notification/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a
                    href="{{ URL::to('admin/notification') }}">{{ trans('labels.firebase_notification') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/notification/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif



            </li>

        @endif




        @if (request()->is('admin/area'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.area') }}</li>
        @endif



        @if (request()->is('admin/area/add*') || request()->is('admin/area/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a href="{{ URL::to('admin/area') }}">{{ trans('labels.area') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/area/edit-*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add_new') }}
                @endif



            </li>

        @endif



        @if (request()->is('admin/plan/selectplan-*'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/plan') }}">{{ trans('labels.plan') }}</a>
            </li>



            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.selected_plan') }}</li>
        @endif


        @if (request()->is('admin/payment'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.payment_settings') }}</li>
        @endif

        @if (request()->is('admin/shipping-area'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.shipping_area') }}</li>
        @endif

        @if (request()->is('admin/shipping-area/add*') || request()->is('admin/shipping-area/show*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page"><a
                    href="{{ URL::to('admin/shipping-area') }}">{{ trans('labels.shipping_area') }}</a></li>

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                @if (request()->is('admin/shipping-area/show*'))
                    {{ trans('labels.edit') }}
                @else
                    {{ trans('labels.add') }}
                @endif

            </li>



        @endif

        @if (request()->is('admin/time'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.working_hours') }}</li>
        @endif

        @if (request()->is('admin/google_analytics'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.analytics') }}</li>
        @endif

        @if (request()->is('admin/categories'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.categories') }}</li>
        @endif

        @if (request()->is('admin/categories/add') || request()->is('admin/categories/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/categories') }}">{{ trans('labels.categories') }}</a>
            </li>

            @if (request()->is('admin/categories/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif





        @if (request()->is('admin/products'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.products') }}</li>
        @endif

        @if (request()->is('admin/products/add') || request()->is('admin/products/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/products') }}">{{ trans('labels.products') }}</a>
            </li>

            @if (request()->is('admin/products/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif

        @if (request()->is('admin/shipping'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.shipping_management') }}</li>
        @endif

        @if (request()->is('admin/shipping/add') || request()->is('admin/shipping/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/shipping') }}">{{ trans('labels.shipping_management') }}</a>
            </li>

            @if (request()->is('admin/shipping/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif



        @if (request()->is('admin/banner'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.banner') }}</li>
        @endif

        @if (request()->is('admin/banner/add') || request()->is('admin/banner/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/banner') }}">{{ trans('labels.banner') }}</a>
            </li>

            @if (request()->is('admin/banner/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif



        @if (request()->is('admin/coupons'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.coupons') }}</li>
        @endif

        @if (request()->is('admin/coupons/add') || request()->is('admin/coupons/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/coupons') }}">{{ trans('labels.coupons') }}</a>
            </li>

            @if (request()->is('admin/coupons/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif



        @if (request()->is('admin/blogs'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.blogs') }}</li>
        @endif

        @if (request()->is('admin/blogs/add') || request()->is('admin/blogs/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/blogs') }}">{{ trans('labels.blogs') }}</a>
            </li>

            @if (request()->is('admin/blogs/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif

        @if (request()->is('admin/subscribers'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.subscribers') }}</li>
        @endif

        @if (request()->is('admin/inquiries'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.inquiries') }}</li>
        @endif

        @if (request()->is('admin/product_inquiry'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.product_inquiry') }}</li>
        @endif

        @if (request()->is('admin/privacy-policy'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.privacy_policy') }}</li>
        @endif

        @if (request()->is('admin/refund-policy'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.refund_policy') }}</li>
        @endif

        @if (request()->is('admin/terms-conditions'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.terms') }}</li>
        @endif

        @if (request()->is('admin/aboutus'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.about_us') }}</li>
        @endif

        @if (request()->is('admin/share'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.share') }}</li>
        @endif

        @if (request()->is('admin/whatsapp_settings'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.whatsapp_settings') }}</li>
        @endif

        @if (request()->is('admin/telegram_settings'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.telegram_settings') }}</li>
        @endif

        @if (request()->is('admin/settings'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.settings') }}</li>
        @endif

        @if (request()->is('admin/users'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.users') }}</li>
        @endif

        @if (request()->is('admin/users/add-*') || request()->is('admin/users/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/users') }}">{{ trans('labels.users') }}</a>
            </li>

            @if (request()->is('admin/users/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif

        @if (request()->is('admin/areas'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.areas') }}</li>
        @endif

        @if (request()->is('admin/areas/add') || request()->is('admin/areas/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/areas') }}">{{ trans('labels.areas') }}</a>
            </li>

            @if (request()->is('admin/areas/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif

        @if (request()->is('admin/cities'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.cities') }}</li>
        @endif

        @if (request()->is('admin/cities/add') || request()->is('admin/cities/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/cities') }}">{{ trans('labels.cities') }}</a>
            </li>

            @if (request()->is('admin/cities/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif



        @if (request()->is('admin/custom_domain'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.custom_domain') }}</li>
        @endif

        @if (request()->is('admin/custom_domain/add') || request()->is('admin/custom_domain/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/custom_domain') }}">{{ trans('labels.custom_domain') }}</a>
            </li>

            @if (request()->is('admin/custom_domain/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif



        @if (request()->is('admin/whoweare'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.who_we_are') }}</li>
        @endif

        @if (request()->is('admin/whoweare/add') || request()->is('admin/whoweare/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/whoweare') }}">{{ trans('labels.who_we_are') }}</a>
            </li>

            @if (request()->is('admin/whoweare/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif


        @if (request()->is('admin/how_works'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.how_works') }}</li>
        @endif

        @if (request()->is('admin/how_works/add') || request()->is('admin/how_works/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/how_works') }}">{{ trans('labels.how_works') }}</a>
            </li>

            @if (request()->is('admin/how_works/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif

        @if (request()->is('admin/themes'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.theme_images') }}</li>
        @endif

        @if (request()->is('admin/themes/add') || request()->is('admin/themes/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/themes') }}">{{ trans('labels.theme_images') }}</a>
            </li>

            @if (request()->is('admin/themes/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif

        @if (request()->is('admin/features'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.features') }}</li>
        @endif

        @if (request()->is('admin/features/add') || request()->is('admin/features/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/features') }}">{{ trans('labels.features') }}</a>
            </li>

            @if (request()->is('admin/features/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif



        @if (request()->is('admin/promotionalbanners'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.promotional_banners') }}</li>
        @endif

        @if (request()->is('admin/promotionalbanners/add') || request()->is('admin/promotionalbanners/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/promotionalbanners') }}">{{ trans('labels.promotional_banners') }}</a>
            </li>

            @if (request()->is('admin/promotionalbanners/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif



        @if (request()->is('admin/faqs'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.faqs') }}</li>
        @endif

        @if (request()->is('admin/faqs/add') || request()->is('admin/faqs/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/faqs') }}">{{ trans('labels.faqs') }}</a>
            </li>

            @if (request()->is('admin/faqs/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif



        @if (request()->is('admin/testimonials'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.testimonials') }}</li>
        @endif

        @if (request()->is('admin/testimonials/add') || request()->is('admin/testimonials/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/testimonials') }}">{{ trans('labels.testimonials') }}</a>
            </li>

            @if (request()->is('admin/testimonials/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif

        @if (request()->is('admin/products/import'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.product_upload') }}</li>
        @endif

        @if (request()->is('admin/media'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/products/import') }}">{{ trans('labels.media') }}</a>
            </li>
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.add') }}
            </li>
        @endif



        @if (request()->is('admin/language-settings') ||
                request()->is('admin/language-settings/en*') ||
                request()->is('admin/language-settings/ar*') ||
                request()->is('admin/language-settings/de*'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.language-settings') }}</li>
        @endif


        @if (request()->is('admin/language-settings/add') || request()->is('admin/language-settings/language/edit-*'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/language-settings') }}">{{ trans('labels.language-settings') }}</a>
            </li>
            @if (request()->is('admin/language-settings/language/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif
        @endif

        @if (request()->is('admin/apps'))
            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">{{ trans('labels.apps') }}</li>
        @endif

        @if (request()->is('admin/createsystem-addons') || request()->is('admin/createsystem-addons/edit-*'))

            <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                aria-current="page">

                <a href="{{ URL::to('admin/apps') }}">{{ trans('labels.addons_manager') }}</a>
            </li>

            @if (request()->is('admin/features/edit-*'))
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.edit') }}</li>
            @else
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-rtl' : '' }}"
                    aria-current="page">{{ trans('labels.add') }}</li>
            @endif

        @endif









    </ol>

</nav>
