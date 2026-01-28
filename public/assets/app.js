document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-copy]').forEach((button) => {
    button.addEventListener('click', () => {
      navigator.clipboard.writeText(button.dataset.copy || '');
      button.textContent = 'Copied';
      setTimeout(() => (button.textContent = 'Copy'), 1500);
    });
  });

  const list = document.querySelector('[data-sortable]');
  if (list) {
    let dragItem = null;
    list.querySelectorAll('.sortable-item').forEach((item) => {
      item.draggable = true;
      item.addEventListener('dragstart', () => {
        dragItem = item;
        item.classList.add('dragging');
      });
      item.addEventListener('dragend', () => {
        item.classList.remove('dragging');
        dragItem = null;
      });
      item.addEventListener('dragover', (event) => {
        event.preventDefault();
        const target = event.currentTarget;
        if (target !== dragItem) {
          const rect = target.getBoundingClientRect();
          const next = (event.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
          list.insertBefore(dragItem, next ? target.nextSibling : target);
        }
      });
    });

    const reorderForm = document.querySelector('[data-reorder-form]');
    if (reorderForm) {
      reorderForm.addEventListener('submit', () => {
        const order = Array.from(list.querySelectorAll('.sortable-item')).map((el) => el.dataset.id);
        reorderForm.querySelector('input[name="order"]').value = order.join(',');
      });
    }
  }
});

window.StudioKitContrast = function (hexA, hexB) {
  const parse = (hex) => {
    if (!hex) return null;
    hex = hex.replace('#', '');
    if (hex.length === 3) {
      hex = hex.split('').map((c) => c + c).join('');
    }
    if (hex.length !== 6) return null;
    const num = parseInt(hex, 16);
    return [(num >> 16) & 255, (num >> 8) & 255, num & 255];
  };

  const lum = (rgb) => {
    const a = rgb.map((v) => {
      v /= 255;
      return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    });
    return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2];
  };

  const rgbA = parse(hexA);
  const rgbB = parse(hexB);
  if (!rgbA || !rgbB) return null;
  const l1 = lum(rgbA) + 0.05;
  const l2 = lum(rgbB) + 0.05;
  return l1 > l2 ? l1 / l2 : l2 / l1;
};
