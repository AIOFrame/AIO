function aio_login_init(e) {
    if( e.keyCode === 13 ) {
        process_data(document.getElementById('aio_login_init'));
    }
}

function aio_forgot_init(e) {
    if( e.keyCode === 13 ) {
        process_data(document.getElementById('aio_forgot_init'));
    }
}

function aio_forgot_view() {
    document.querySelector('.login_wrap').style.display = 'none';
    document.querySelector('.forgot_wrap').style.display = 'block';
}

function aio_login_view() {
    document.querySelector('.login_wrap').style.display = 'block';
    document.querySelector('.forgot_wrap').style.display = 'none';
}