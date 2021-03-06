<!DOCTYPE html>

<head>
    <title>Tụng kinh niệm phật</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js" integrity="sha512-6+YN/9o9BWrk6wSfGxQGpt3EUK6XeHi6yeHV+TYD2GR0Sj/cggRpXr1BrAQf0as6XslxomMUxXp2vIl+fv0QRA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<style>

</style>
<script>
    var prod = !location.href.startsWith('file');
    if (prod) {
        var sound = new Howl({
        src: ['beat.mp3']
        });
    }

    $(document).ready(function(){
        if (localStorage.current) {
            $('#result').html(localStorage.current);
            $('#current').val(localStorage.current);
        }
        if (localStorage.rpm) {
            $('#rpm').val(localStorage.rpm);
        }
        if (localStorage.count) {
            $('#count').val(localStorage.count);
        }
        if (localStorage.mute) {
            $('#mute').val(localStorage.mute);
        }
        if (localStorage.sentence) {
            $('#sentenceBn').html(localStorage.sentence);
            $('#sentence').val(localStorage.sentence);
        }

        setInterval(function(){$('#clock').html(getTime())}, 1000);

        var result ,interval, rpm, total, count, mute, sentence;

        var callback = function(){
            var tick = result%total == 0 ? total : result%total;
            if (result % total == 0) {
                $('#result').html(result/total);
                $('#current').val(result/total);
                localStorage.setItem('current',result/total);
            }
            var tickContent = '';
            if (tick <= count) tickContent = tick % total;
            else {
                switch ((tick-count-1)%3) {
                    case 0: tickContent = '.'; break;
                    case 1: tickContent = '..'; break;
                    case 2: tickContent = '...'; break;
                }
            }
            $('#tick').html(tickContent);
            $('#sentenceBn span').removeClass('text-success');
            if (tick <= count) {
                if (prod) sound.play();
                $(`#sentenceBn span:nth-child(${tick})`).toggleClass('text-success');
            }
            result+=1;
        }

        function getTime(){
            var d = new Date();
            return datestring = (d.getHours()<10?'0':'') + d.getHours() + ":" + (d.getMinutes()<10?'0':'') + d.getMinutes() + ":" + (d.getSeconds()<10?'0':'') + d.getSeconds();
        }

        $('#play').click(function(){
            sentence = $('#sentence').val();
            if (!sentence) {
                alert('Vui lòng nhập câu thần chú!');
                return;
            }
            if (this.classList.contains('disabled')) return;
            // remove all double spaces, trim
            sentence = sentence.replace(/\n/g,' ').replace(/ +(?= )/g,'').trim();
            $('#sentence').val(sentence);
            sentence = sentence.split(' ');
            document.querySelector('#sentenceBn').innerHTML = '';
            sentence.forEach((x,y) => document.querySelector('#sentenceBn').innerHTML += `<span id='${y}' class='mr-2 d-inline-block'>${x}</span>`);
            
            this.classList.add('disabled');
            $('input,textarea').prop("disabled",true);
            document.querySelector('#pause').classList.remove('disabled');
            var current = parseInt($('#current').val());
            var mute = parseInt($('#mute').val());
            rpm = parseInt($('#rpm').val());
            count = sentence.length;
            localStorage.setItem('rpm',$('#rpm').val());
            localStorage.setItem('count',sentence.length);
            localStorage.setItem('mute',$('#mute').val());
            localStorage.setItem('sentence',$('#sentence').val());
            
            total = count+mute;
            result = current*(total);
            gap = 60/rpm*1000;
            interval = setInterval(callback, gap);
        })

        $('#pause').click(function(){
            if (this.classList.contains('disabled')) return;
            this.classList.add('disabled');
            $('input,textarea').prop("disabled",false);
            document.querySelector('#play').classList.remove('disabled');

            clearInterval(interval);
        })
    })
</script>

<body class="bg-dark text-white">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-8 offset-md-2">
                <div class="pt-4 text-center">
                    <button class="btn btn-primary mr-3 px-4 py-4" id="play">Phát</button>
                    <button class="btn btn-warning mr-3 px-4 py-4 disabled" id="pause">Tạm Dừng</button>
                    <button class="btn btn-danger mr-3 px-4 py-4" id="reset" onclick="(function(){localStorage.clear();location.reload()})()">Đặt Lại</button>
                    <div>
    
                        <table class="my-3 h4 text-center w-100">
                        <tr>
                            <td class="px-2">Đã đọc (lần)</td>
                            <td class="px-2">Nhịp hiện tại</td>
                            <td class="px-2">Bây giờ là</td>
                        </tr>
                        <tr>
                            <td><span id="result">0</span></td>
                            <td><span id="tick">0</span></td>
                            <td><span id="clock"></span></td>
                        </tr>
                    </table>
                    </div>
                </div>
                <div class="font-weight-bold mt-3">
                    <h3 id="sentenceBn" class="text-center bg-secondary">Om ma ni pad me hum</h3>
                    <table class="d-flex justify-content-center my-3">
                        <tr>
                            <td colspan="2">
                                Nhập câu thần chú:</b><textarea class="w-100 my-2" rows="3" id="sentence" value="Om ma ni pad me hum" placeholder="Nhập câu thần chú..."></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="pr-3 py-2">Số lần đã đọc:</td><td><input id="current" value="0"></td>
                        </tr>
                        <tr>
                            <td class="pr-3 py-2">Nhịp tụng/phút (rpm):</td><td><input id="rpm" value="198"></td>
                        </tr>
                        <tr>
                            <td class="pr-3 py-2">Số nhịp nghỉ giữa 2 chu kỳ:</td><td><input id="mute" value="5"></td>
                        </tr>
                    </table>
                </div>

                <div>
                    <h4>Hướng dẫn sử dụng:</h3>
                    Quá trình trì chú yêu cầu hành giả phải đọc trong thời gian dài, cần nhớ số lần đã đọc được, cần thời gian nghỉ giữa các lần đọc. Xuất phát từ yêu cầu đó, trang web này được tạo ra nhằm mục đích hỗ trợ cô/chú/anh/chị trong việc trì chú tại gia. Các thông số có thể điều chỉnh được bằng ô nhập liệu.<br>
                    * Nhập câu thần chú: Nhập câu thần chú mà bạn muốn đọc vào mục này theo dạng âm, những từ ghép cần tách ra. Ví dụ: Tadyatha -> ta dy a tha.<br>
                    * Số lần đã đọc: Lưu lại số lần đã đọc, hết một chu kỳ tăng lên 1.<br>
                    * Nhịp tụng/phút: 1 phút sẽ gõ phách bao nhiêu lần. Có thể điều chỉnh tùy nhịp đọc/thở.<br>
                    * Số nhịp nghỉ giữa 2 chu kỳ: Sau một câu cần một thời gian nghỉ để lấy hơi. Câu ngắn có thể đọc 2-3 câu rồi nghỉ 1 lần (thêm vào mục nhập câu thần chú). Khi đến nhịp nghỉ thì sẽ <span class="text-info">không phát ra tiếng gõ</span>, vì qua trải nghiệm tác giả thấy nên giảm thiểu tiếng động do thời gian trì tụng dài.<br>
                    Tốc độ có thể điều chỉnh khi mở trang, hoặc ấn nút 'Tạm Dừng'. Khi hành giả thoát trang web và quay trở lại, nếu không xóa lịch sử duyệt web thì số lần đọc vẫn được lưu lại cho đến khi ấn 'Đặt Lại', tất cả thông số sẽ trở về ban đầu.<br>
                    Chúc các cô/chú/anh/chị đạt được kết quả tốt khi sử dụng trang web.<br>
                    List thần chú tham khảo: <a class="text-info" href="https://wisdomofcompassion.wixsite.com/home/minhchu" target="_blank">Thần chú chính tông - wisdomofcompassion</a>
                    <div class="text-right mt-2">Tác giả: Khánh Nguyễn <a class="text-info" href="mailto:khanhthietbi@gmail.com">(liên hệ)</a></div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>