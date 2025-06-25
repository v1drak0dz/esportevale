<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
<script>
    $('#content').summernote({
        height: 250,
        callbacks: {
            onImageUpload: function(files) {
                let data = new FormData();
                data.append("image", files[0]);

                $.ajax({
                    url: '/news/upload',
                    type: 'POST',
                    data: data,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(response) {
                        $('#content').summernote('insertImage', response);
                    },
                    error: function() {
                        alert('Erro ao fazer upload da imagem.')
                    }
                })
            }
        }
    });
    
    $('#league_content_table').summernote({
        height: 250
    });
    $('#league_content_rounds').summernote({
        height: 250
    });
</script>
</body>
</html>