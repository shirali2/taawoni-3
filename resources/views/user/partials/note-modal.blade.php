@php
  try {
    $pendingNotes = \App\note::where('user_id', session('id_user'))
        ->approved()
        ->visibleToUser()
        ->unseenByUser()
        ->notArchived()
        ->orderBy('created_at')
        ->get();
  } catch (\Throwable $e) {
    $pendingNotes = collect([]);
  }
@endphp

@if($pendingNotes->isNotEmpty())
<div id="note-modal-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:9999;display:flex;align-items:center;justify-content:center;">
  <div id="note-modal-box" style="background:#fff;border-radius:8px;padding:24px;max-width:500px;width:90%;position:relative;direction:rtl;text-align:right;">

    <span id="note-modal-close" style="position:absolute;top:12px;left:12px;cursor:pointer;font-size:18px;">&times;</span>

    <h3 style="margin-bottom:16px;">یادداشت جدید</h3>
    <div id="note-modal-counter" style="font-size:0.85em;color:#666;margin-bottom:8px;"></div>
    <div id="note-modal-content" style="font-size:0.95em;line-height:1.7;margin-bottom:20px;white-space:pre-wrap;"></div>

    <button id="note-modal-seen-btn" style="background:#007bff;color:#fff;border:none;padding:10px 20px;border-radius:4px;cursor:pointer;">
      مشاهده کردم
    </button>
  </div>
</div>

<script>
(function() {
  var notes = {!! $pendingNotes->toJson() !!};
  var current = 0;
  var overlay = document.getElementById('note-modal-overlay');
  var closeBtn = document.getElementById('note-modal-close');
  var seenBtn  = document.getElementById('note-modal-seen-btn');
  var contentEl = document.getElementById('note-modal-content');
  var counterEl = document.getElementById('note-modal-counter');
  var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function showNote(index) {
    var note = notes[index];
    contentEl.textContent = note.content;
    counterEl.textContent = 'یادداشت ' + (index + 1) + ' از ' + notes.length;

    if (note.is_mandatory) {
      closeBtn.style.display = 'none';
      overlay.onclick = function(e) { e.stopPropagation(); };
    } else {
      closeBtn.style.display = 'block';
      overlay.onclick = function(e) { if (e.target === overlay) overlay.style.display = 'none'; };
    }
  }

  closeBtn.onclick = function() { overlay.style.display = 'none'; };

  seenBtn.onclick = function() {
    var note = notes[current];
    seenBtn.disabled = true;
    fetch('{{ url("/user/notes") }}/' + note.id + '/mark-as-seen', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        current++;
        if (current < notes.length) {
          seenBtn.disabled = false;
          showNote(current);
        } else {
          overlay.style.display = 'none';
        }
      }
    })
    .catch(function() { seenBtn.disabled = false; });
  };

  showNote(0);
})();
</script>
@endif
