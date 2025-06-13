const container = document.querySelector('.recensione-container');
const freccia_sinistra = document.querySelector('.scroll-btn.left');
const freccia_destra = document.querySelector('.scroll-btn.right');
const cards = container.querySelectorAll('.recensione-card');

function scrollByCard(direction) {
    const cardWidth = cards[0].offsetWidth + parseInt(getComputedStyle(container).gap);
    if(direction === 'left') {
        container.scrollBy({ left: -cardWidth, behavior: 'smooth' });
    } else {
        container.scrollBy({ left: cardWidth, behavior: 'smooth' });
    }
}

window.addEventListener('load', () => {
    const gap = parseInt(getComputedStyle(container).gap);
    const cardWidth = cards[0].offsetWidth;
    const totalWidth = cards.length * cardWidth + (cards.length - 1) * gap;
    const containerWidth = container.offsetWidth;

    if(cards.length <= 3 && totalWidth < containerWidth) {
        const paddingLeft = (containerWidth - totalWidth) / 2;
        
        //Il padding a sinistra serve per centrare le card al centro della pagina se sono <= 3
        container.style.paddingLeft = `${paddingLeft}px`;
        container.scrollLeft = 0;

        //Nascondo i bottoni se card <= 3
        freccia_sinistra.style.display = 'none';
        freccia_destra.style.display = 'none';

    } else {
        container.style.paddingLeft = '0px';
        container.scrollLeft = 0;
    }
});

freccia_sinistra.addEventListener('click', () => scrollByCard('left'));
freccia_destra.addEventListener('click', () => scrollByCard('right'));
