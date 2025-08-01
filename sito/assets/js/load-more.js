document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.data-container').forEach(container => {
    let page = 1;

    const parent = container.parentNode;

    const button = parent.querySelector('.load-more');
    const status = parent.querySelector('.a11y-status');

    button.addEventListener('click', async () => {
      button.disabled = true;

      try {
        const response = await fetch(`load-more.php?page=${page}`);
        const html = await response.text();

        if (html.trim()) {
          container.insertAdjacentHTML('beforeend', html);

          status.textContent = "Nuovi elementi caricati";
          button.textContent = "Carica di più";
          button.disabled = false;
          page++;
        } else {
          button.textContent = "Tutti i contenuti caricati";
          button.disabled = true;
          status.textContent = "Fine dei contenuti";
        }
      } catch (error) {
        status.textContent = "Errore nel caricamento";
        button.disabled = false;
        console.error("Errore fetch:", error);
      }
    });
  });
});