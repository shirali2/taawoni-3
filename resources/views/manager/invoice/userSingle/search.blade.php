@extends("theme.manager")
@section("container")
  <style type="text/css">
    @media screen and (min-width: 451px){
      #id-or-hash-user-or-receipt-number-invoice{
        width: 335px
      }
    }
    @media screen and (max-width: 450px){
      #id-or-hash-user-or-receipt-number-invoice{
        width: 245px;
        font-size: 10px
      }
      .buttons{
        float: right;
        width: 100%;
        margin-bottom: 10px
      }
    }
  </style>

  <div class="card-box table-responsive">
    <div class="m-b-30">
      <h4 class="header-title d-inline-block">جستجوی صورتحساب های کاربر</h4>
    </div>

    <div class="text-center">
      <input
        type="text"
        inputmode="numeric"
        pattern="[0-9]*"
        placeholder="شناسه کاربر ..."
        id="id-or-hash-user-or-receipt-number-invoice"
        class="p-2"
        autocomplete="off"
      >
      <div id="user-id-error" class="text-danger mt-2" style="display:none;font-size:13px;">
        شناسه کاربر باید عدد صحیح باشد
      </div>

      <div class="float-right w-100 mt-4">
        <a class="buttons d-inline-block bg-primary text-white p-3">صورتحساب ها</a>
        <a class="buttons bg-warning text-white p-3">صورتحساب نهایی</a>
        <a class="buttons bg-success text-white p-3">لیست پرداختی ها</a>
      </div>
    </div>
  </div>

  <script type="text/javascript" language="JavaScript">
    var $searchInput = $('#id-or-hash-user-or-receipt-number-invoice');
    var $errorMsg    = $('#user-id-error');

    // Block non-numeric keystrokes at the keyboard level
    $searchInput.on('keydown', function(e) {
      var allowed = [
        'Backspace','Delete','ArrowLeft','ArrowRight','ArrowUp','ArrowDown',
        'Tab','Home','End','Enter'
      ];
      if (allowed.indexOf(e.key) !== -1) return;
      if (e.key >= '0' && e.key <= '9') return;
      e.preventDefault();
    });

    // Strip any non-digit characters that slip in via paste / autofill
    $searchInput.on('input', function() {
      var raw     = $(this).val();
      var cleaned = raw.replace(/[^0-9]/g, '');
      if (raw !== cleaned) {
        $(this).val(cleaned);
      }

      var val = $(this).val();

      if (val === '') {
        $errorMsg.hide();
        $('.buttons').removeAttr('href');
        return;
      }

      if (!/^[0-9]+$/.test(val) || parseInt(val, 10) < 1) {
        $errorMsg.show();
        $('.buttons').removeAttr('href');
        return;
      }

      $errorMsg.hide();
      $('.buttons').each(function(counter_number) {
        var action = (counter_number === 0) ? 'invoices' : '';
        action = (counter_number === 1) ? 'final'    : action;
        action = (counter_number === 2) ? 'payments' : action;
        $(this).attr('href', '/manager/invoice/user/single/' + action + '/' + val);
      });
    });
  </script>
@endsection