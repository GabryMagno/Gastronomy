
#let copertina(
  doc,
  link_website: "http://tecweb.studenti.math.unipd.it/nome_cognome/index.php",
  link_text: "tecweb.studenti.math.unipd.it/nome_cognome/index.php",
) = {
  set text(lang: "it")
  set list(indent: 1em)
  set enum(indent: 1em)
  set align(center)
  
  text(25pt, weight: "bold", fill: black, font: "Liberation Sans")[CUOCHI PER CASO \ ]

  circle(radius: 7em, fill: rgb("#73142F"))[
    #image("../assets/img/loghi/logo_scritta_bianca.png", height: 100%)
  ]

  v(1.5em)


  show grid.cell.where(x: 0): cell => align(right, cell)
  show grid.cell.where(x: 1): cell => align(left, cell)

  text(15pt, weight: "bold", fill: black, font: "Liberation Sans")[
    #box(
      width: 80%,
      table(
        stroke: none,
        columns: (50%, 50%),
        inset: 8pt,
        table.cell(colspan: 2, stroke: none)[*AUTORI* \ \ ],
        [Gabriele Magnelli], [2075542],
        [Nicolò Bolzon], [2075521],
      ),
    )
  ]

  v(1em)

  link(link_website)[#text(fill: navy, size: 1.3em)[#underline(link_text)]]

  v(1em)

  text(13pt, fill: black, font: "Liberation Sans")[
    #box(
      width: 50%,
      table(
        columns: (auto, auto),
        inset: 0.5em,
        table.cell(colspan: 2, stroke: none)[*DATI PER ACCEDERE* \ \ ],
        [*Username*], [*Password*],
        [user], [user],
        [Gabry], [aA1!aaaa],
      ),
    )

    \ *Referente* \
    Nicolò Bolzon \
    #link("mailto:nicolo.bolzon@studenti.unipd.it")[#text(fill: navy, size: 1em)[#underline("nicolo.bolzon@studenti.unipd.it")]]

  ]


  set text(12pt, font: "DejaVu Serif")

  set par(justify: true)

  set page(
    numbering: "1",
    header: [
      #set text(12pt, font: "DejaVu Serif")
      #grid(
        columns: (1fr, 1fr),
        align(left)[CUOCHI PER CASO],
        align(right)[Relazione Progetto TecWeb],
      )
      #line(length: 100%)
    ],
    footer: [
      #set text(12pt, font: "DejaVu Serif")
      #set align(center)
      #line(length: 100%)
      #context [
        Pagina #counter(page).display(page.numbering) di #counter(page).final().first()
      ]
    ],
  )

  set align(left)
  set heading(numbering: "1.")
  counter(page).update(1)
  pagebreak()

  show outline.entry.where(level: 1): it => {
    v(12pt, weak: true)
    strong(it)
  }

  outline(title: [#v(2em) INDICE #v(3em)], indent: auto)
  pagebreak()

  doc
}
