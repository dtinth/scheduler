
require 'pygments'

data = $stdin.read
template = File.read('template.tex')

data['\includegraphics{er-diagram.png}'] =
  '\begin{center}\centering\includegraphics[width=0.8\textwidth]{../er-diagram.pdf}\end{center}'

data.gsub! (/\\begin\{verbatim\}\n([\s\S]*?)\\end\{verbatim\}/) do
  Pygments.highlight($1, :lexer => 'sql', :formatter => 'latex')
end

template['COMMANDS'] = `python scripts/gen-style-def.py`
template['CONTENTHERE'] = data

puts template

