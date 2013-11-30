
data = $stdin.read
template = File.read('template.tex')

data['\includegraphics{er-diagram.png}'] =
  '\begin{center}\centering\includegraphics[width=0.8\textwidth]{../er-diagram.pdf}\end{center}'

template['CONTENTHERE'] = data

puts template

