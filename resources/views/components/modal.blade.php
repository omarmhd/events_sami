{{--
 | <x-modal> — مكوّن مودال موحد
 |
 | Props:
 |   id          (string, required)  معرّف فريد للمودال
 |   title       (string, optional)  عنوان المودال — إذا تُرك فارغاً لا يظهر الهيدر
 |   subtitle    (string, optional)  وصف صغير تحت العنوان
 |   size        (string, optional)  sm | md (default) | lg | xl
 |   static      (bool,   optional)  true = يمنع الإغلاق عند الضغط خارج المودال
 |   class       (string, optional)  كلاسات إضافية على div.modal
 |
 | Slots:
 |   $slot                           المحتوى الرئيسي (modal-body + modal-footer بالكامل)
 |
 | Usage:
 |   <x-modal id="myModal" title="عنوان" size="lg" :static="true">
 |       <div class="modal-body"> ... </div>
 |       <div class="modal-footer"> ... </div>
 |   </x-modal>
--}}

@props([
    'id',
    'title'    => null,
    'subtitle' => null,
    'size'     => null,
    'static'   => false,
    'class'    => '',
])

<div class="modal fade app-modal {{ $class }}"
     id="{{ $id }}"
     tabindex="-1"
     aria-hidden="true"
     @if($static) data-bs-backdrop="static" data-bs-keyboard="false" @endif>

    <div class="modal-dialog modal-dialog-centered {{ $size ? 'modal-'.$size : '' }}">
        <div class="modal-content app-modal-content">

            @if($title)
            <div class="modal-header app-modal-header">
                <div>
                    <h5 class="modal-title app-modal-title">{{ $title }}</h5>
                    @if($subtitle)
                    <p class="app-modal-subtitle">{{ $subtitle }}</p>
                    @endif
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            @endif

            {{ $slot }}

        </div>
    </div>
</div>
