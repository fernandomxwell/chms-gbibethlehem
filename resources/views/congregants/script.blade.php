<script>
    $(document).ready(function() {
        const maleHonorifics = ['bpk', 'sdr'];
        const femaleHonorifics = ['ibu', 'sdri'];

        $('#honorific_title').change(function () {
            const honorific = $(this).val();
            if (maleHonorifics.includes(honorific)) {
                $('#gender').val('male');
            } else if (femaleHonorifics.includes(honorific)) {
                $('#gender').val('female');
            }
        });

        $('#gender').change(function () {
            const gender = $(this).val();
            const honorific = $('#honorific_title').val();
            if (gender === 'male' && femaleHonorifics.includes(honorific)) {
                $('#honorific_title').val('');
            } else if (gender === 'female' && maleHonorifics.includes(honorific)) {
                $('#honorific_title').val('');
            }
        });

        $('#date_of_birth').change(function () {
            const date = $(this).val();

            $('#date_of_baptism').attr('min', date);
        });
    });
</script>
