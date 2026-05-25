<script>
    document.getElementById('country')?.addEventListener('change', function () {
        document.getElementById('city').disabled = this.value === '';
    });

    function previewFile(inputId, imageId) {
        const input = document.getElementById(inputId);
        input?.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                document.getElementById(imageId).src = URL.createObjectURL(this.files[0]);
            }
        });
    }

    previewFile('vdimg', 'vdpimg');
    previewFile('posbgimg', 'posbimg');
    previewFile('ordercallingbgimg', 'previewordercallingbgimg');
</script>
