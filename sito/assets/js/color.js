const menuLinks = document.querySelectorAll('#menu a');

menuLinks.forEach(link => {
  // Controlla se l'URL è già salvato in localStorage
  if(localStorage.getItem(link.href)) {
    link.classList.add('visited-link');
  }

  // Al click, salva lo stato nel localStorage e aggiungi la classe
  link.addEventListener('click', () => {
    localStorage.setItem(link.href, true);
    link.classList.add('visited-link');
  });
});