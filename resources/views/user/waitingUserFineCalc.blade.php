<html>
<body dir="rtl" style="font-family:'Tahoma';">
    <div style="justify-content:center; display:flex;height:100%;align-items:center;font-size:xx-large;">
        <span> در حال محاسبه جریمه صورتحساب ها ...</span>
    </div>
    <script src="/assets/js/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: '/user/full_fine_calc'
                , success: function(result) {
                    if (result.success) {} else {
                        alert(result.msg);
                    }
                    window.location.href = "/user";
                }
                , error: function(error) {
                    alert('عملیات با خطا مواجه شده است');
                    window.location.href = "/user";
                }
            });
        });

    </script>
</body>
</html>
