$().ready(function () {
    var dialog = $('#default-dialog').modal({
        show: false
    });

    $('.link-as-dialog').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $.get($this.attr('href'), function (data) {
            dialog.find('.modal-title').text($this.hasClass('create') ? 'Создание' : 'Изменение');
            dialog.find('.modal-body').html(data);
            dialog.modal('show');
        });
    });

    $('.link-toggle-active').click(function (e) {
        e.preventDefault();
        var $this = $(this), isActive = $this.data('active');
        $.get($this.attr('href'), function (active) {
            isActive = active;
            $this.data('active', active);
            $this.text(isActive === '1' ? 'Заблокировать' : 'Активировать');
            if (isActive === '1') {
                $this.addClass('btn-success').removeClass('btn-danger');
            } else {
                $this.addClass('btn-danger').removeClass('btn-success');
            }
        });
    });
});