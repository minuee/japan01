$(function () {

    $(document).on("change", "#ScheduleTeam,#ProjectGroup", function () {
        $("#IsOnlyme").val(0);
        $('#calendar').fullCalendar('rerenderEvents');
    });

    $(document).on("click", "#btn_view_onlyme", function () {
        if ( $(this).hasClass("btn-default") ){
            $("#IsOnlyme").val(1);
            $(this).removeClass("btn-default");
            $(this).addClass("btn-primary");
            $(this).text("전체보기");
        }else{
            $("#IsOnlyme").val(0);
            $(this).addClass("btn-default");
            $(this).removeClass("btn-primary");
            $(this).text("내 일정만 보기");
        }
        $("#ScheduleTeam").find("option:eq(0)").prop("selected", true);
        $('#calendar').fullCalendar('rerenderEvents');
        return false;
    });


});

function rgb2hex(rgb){
    rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
    return (rgb && rgb.length === 4) ? "#" +
        ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
}

function pageprint(event)
{
    html2canvas($('#calendar'), {
        logging: true,
        useCORS: true,
        background :'#FFFFFF',
        onrendered: function (canvas) {
            var imgData = canvas.toDataURL("image/jpeg");
            var doc = new jsPDF();
            doc.addImage(imgData, 'JPEG', 15, 40, 180, 160);
            download(doc.output(), "Schedule.pdf", "text/pdf");
        }
    }) ;
}

function download(strData, strFileName, strMimeType)
{
    var D = document,
        A = arguments,
        a = D.createElement("a"),
        d = A[0],
        n = A[1],
        t = A[2] || "text/plain";

    //build download link:
    a.href = "data:" + strMimeType + "," + escape(strData);

    if (window.MSBlobBuilder) {
        var bb = new MSBlobBuilder();
        bb.append(strData);
        return navigator.msSaveBlob(bb, strFileName);
    } /* end if(window.MSBlobBuilder) */

    if ('download' in a) {
        a.setAttribute("download", n);
        a.innerHTML = "downloading...";
        D.body.appendChild(a);
        setTimeout(function() {
            var e = D.createEvent("MouseEvents");
            e.initMouseEvent("click", true, false, window, 0, 0, 0, 0, 0, false, false,
                false, false, 0, null);
            a.dispatchEvent(e);
            D.body.removeChild(a);
        }, 66);
        return true;
    } /* end if('download' in a) */

    //do iframe dataURL download:
    var f = D.createElement("iframe");
    D.body.appendChild(f);
    f.src = "data:" + (A[2] ? A[2] : "application/octet-stream") + (window.btoa ? ";base64"
        : "") + "," + (window.btoa ? window.btoa : escape)(strData);
    setTimeout(function() {
        D.body.removeChild(f);
    }, 333);
    return true;
}