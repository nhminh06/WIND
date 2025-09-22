
document.querySelectorAll('.tabs span').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.tabs span').forEach(t => t.classList.remove('active-tab'));
    tab.classList.add('active-tab');
  });
});

