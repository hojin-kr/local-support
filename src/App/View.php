<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <title>경기지역화폐 가맹점 지도에서 보기</title>
    <script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?ncpClientId=z3s5m464oj&submodules=geocoder"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap');
        body {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-family: 'Roboto', sans-serif;
        background-color: #F5F5F8;
    }
        #function {
            height: 5em;
        }
        .btn {
            border: 0em solid aliceblue;
            border-radius: 0.5em;
            background-color: #FFFFFF;
            box-shadow: 0.1em 0.1em 0.1em 0.1em #D3D3D3;
            padding: 1em;
            margin: 0.5em;
            font-weight: 600;
            color: #171D2E;
        }
        .btn-flat {
            border: 0em solid aliceblue;
            border-radius: 0.5em;
            background-color: #FFFFFF;
            padding: 1.5em;
            margin: 1em;
            font-weight: 600;
            color: #171D2E;
        }
        .inner-desc {
            padding: 0.5em;
            border: 0em solid aliceblue;
            border-radius: 0.5em;
        }
        #notice {
            color: #171D2E;
        }
        .hover:hover {
            background-color: #171D2E;
            color: #FFFFFF;
        }
        a {
            color: #171D2E;
        }
        footer {
            font-size: small;
            margin: 0.2em;
        }
        header {
            font-size: small;
            margin: 0.2em;
        }
    </style>
</head>
<body>
<header id="header">경기지역화폐 가맹점</header>
<div id="banner">
    <button class="btn ad-banner">
        <a href="https://github.com/hojin-kr/local-support">배너 광고</a>
    </button>
</div>
<div id="map"></div>
<div id="function">
    <button id="btn-center" class="btn hover">내 위치로 이동</button>
    <button id="btn-search" class="btn hover">현 위치에서 검색</button>
    <button id="btn-request" class="btn hover">문의하기</button>
</div>
<footer id="footer">(c) jhj377@gmail.com</footer>
<script>

let globalLat = 37.385296486885
let globalLng = 127.13012321735

$("#btn-search").on("click", ()=>{
    var latlng = map.getCenter();
    searchCoordinateToAddress(latlng)
})

$("#btn-center").on("click", ()=>{
    setCurrentPosition()
})

$("#btn-request").on("click", ()=>{
    msg = prompt("문의 내용을 입력해주세요.")
    if (msg != null) {
        $.ajax({
            method: "GET",
            url: Domain + "/slack/?msg="+msg,
        })
        .done(function( msg ) {
            alert("문의 내용이 개발자에게 전송되었습니다.")
        })
    }
})

var map = new naver.maps.Map('map', {
    center: new naver.maps.LatLng(globalLat, globalLng),
    zoom: 15,
    mapTypeId: naver.maps.MapTypeId.NORMAL
});

var infowindow = new naver.maps.InfoWindow();
function onSuccessGeolocation(position) {
    var location = new naver.maps.LatLng(position.coords.latitude,
                                         position.coords.longitude);
    globalLat = position.coords.latitude;
    globalLng = position.coords.longitude;

    map.setCenter(location); // 얻은 좌표를 지도의 중심으로 설정합니다.
    map.setZoom(17); // 지도의 줌 레벨을 변경합니다.

    // infowindow.setContent('<div style="padding:20px;">' + 'geolocation.getCurrentPosition() 위치' + '</div>');

    // infowindow.open(map, location);
    console.log('Coordinates: ' + location.toString());
    searchCoordinateToAddress(location)
}

function onErrorGeolocation() {
    var location = new naver.maps.LatLng(globalLat, globalLng);

    map.setCenter(location); // 얻은 좌표를 지도의 중심으로 설정합니다.
    map.setZoom(18); // 지도의 줌 레벨을 변경합니다.
}

$(window).on("load", function() {
    setCurrentPosition()
});

function setCurrentPosition() {
    if (navigator.geolocation) {
        /**
         * navigator.geolocation 은 Chrome 50 버젼 이후로 HTTP 환경에서 사용이 Deprecate 되어 HTTPS 환경에서만 사용 가능 합니다.
         * http://localhost 에서는 사용이 가능하며, 테스트 목적으로, Chrome 의 바로가기를 만들어서 아래와 같이 설정하면 접속은 가능합니다.
         * chrome.exe --unsafely-treat-insecure-origin-as-secure="http://example.com"
         */
        navigator.geolocation.getCurrentPosition(onSuccessGeolocation, onErrorGeolocation);
    } else {
        var center = map.getCenter();
        // infowindow.setContent('<div style="padding:20px;"><h5 style="margin-bottom:5px;color:#f00;">Geolocation not supported</h5></div>');
        // infowindow.open(map, center);
    }
}


// 네이버 맵 크기조정
window.addEventListener('DOMContentLoaded', function(){
    resize();
    window.addEventListener('resize', resize);
});

function resize(){
    var mapWidth = window.innerWidth
    var mapHeight = window.innerHeight - document.getElementById('banner').offsetHeight - document.getElementById('function').offsetHeight - 55
    var Size = new naver.maps.Size(mapWidth, mapHeight)
    map.setSize(Size)
}

let Domain = window.location.protocol + "//" + window.location.hostname
let infowindows = []

function getShops(sigungu, latlng) {
        $.ajax({
        method: "GET",
        url: Domain + "/shop/?sigungu=" + sigungu +"&lat="+ String(latlng._lat) + "&lng=" + String(latlng._lng),
        })
        .done(function( msg ) {
            if (msg == "null") {
                return 0
            }
            let shops = JSON.parse(msg)
            console.log(shops.length)
            if (shops.length == 0) {
                alert("검색 준비중인 지역입니다. [ "+ sigungu +" ] \n *성남, 김포, 시흥 등 3개 시군은 현재 데이터 가공중으로 추후 업데이트 예정입니다.")
            }
            let latlngs = []
            let infos = []
            // 범위 기준값
            for (const [key, value] of Object.entries(shops)) {
                latlngs.push(new naver.maps.LatLng(value[10], value[11]))
                infos.push(value)
            }
            for (var i=0, ii=latlngs.length; i<ii; i++) {
                let marker = new naver.maps.Marker({
                        position: latlngs[i],
                        map: map,
                    })

                marker.set('seq', i);
                marker.set('category', infos[i][6])

                var contentString = [
                        '<div class="iw_inner inner-desc">',
                        '   <h4>' + infos[i][1] + '</h4>',
                        '   <p> 전화 : ' + infos[i][4] + ' <br />',
                        '   <p> 주소 : ' + infos[i][7] + ' <br />',
                        '   </p>',
                        '</div>'
                    ].join('');

                infowindows.push(new naver.maps.InfoWindow({
                    content: contentString
                }))
                marker.addListener('mouseover', onMouseOver);
                marker.addListener('mouseout', onMouseOut);
                marker.addListener('click',onMouseOver)
            }
            $("#btn-search").text("현 위치에서 검색")
        })
    }

function onMouseOver(e) {
    let marker = e.overlay,
        seq = marker.get('seq');
    if (infowindows[seq].getMap()) {
        infowindows[seq].close();
    } else {
        infowindows[seq].open(map, marker);
    }
}

function onMouseOut(e) {
    var marker = e.overlay,
        seq = marker.get('seq');
}

function searchCoordinateToAddress(latlng) {
    $("#btn-search").text("검색중 ...")
    naver.maps.Service.reverseGeocode({
    coords: latlng,
    }, function(status, response) {
    if (status === naver.maps.Service.Status.ERROR) {
        if (!latlng) {
        return alert('ReverseGeocode Error, Please check latlng');
        }
        if (latlng.toString) {
        return alert('ReverseGeocode Error, latlng:' + latlng.toString());
        }
        if (latlng.x && latlng.y) {
        return alert('ReverseGeocode Error, x:' + latlng.x + ', y:' + latlng.y);
        }
        return alert('ReverseGeocode Error, Please check latlng');
    }

    var address = response.v2.address
    sigungu = (address.jibunAddress).split(' ')[1]
    console.log(address.jibunAddress)
    console.log(sigungu)
    getShops(sigungu, latlng)
    });
}

</script>
</body>
</html>