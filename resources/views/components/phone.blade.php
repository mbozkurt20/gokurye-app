
<input type="tel"
       name="{{ $key }}"
       id="{{ $key }}"
       placeholder="(555) 555-5555"
       autocomplete="tel"
       maxlength="14"
       value="{{$value}}"
       class="{{$class ?? 'form-control border border-light'}}"
       pattern="[(][0-9]{3}[)] [0-9]{3}-[0-9]{4}"
    {{ $required ? 'required' : '' }} />

@once
    <script>
        document.addEventListener('input', function (e) {
            if(e.target.matches('input[type="tel"]')) {
                var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
            }
        });
    </script>
@endonce
