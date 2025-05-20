<script>
  window.LaravelEnv = {
    APP_URL: '{{ env('APP_URL') }}',
    RCN_API_URL: '{{ env('RCN_API_URL') }}'
  };
</script>
<script src="{{ mix('js/app.js') }}"></script>
