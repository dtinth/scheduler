
require 'csv'
require 'yaml'
require 'to_latex'

input = ARGV[0]
output = ARGV[1]

puts "#{input} => #{output}"

data = CSV.table(input, :converters => nil)
styles = YAML.load(File.read('table-style.yml'))
name = File.basename(input, '.csv')

style = styles[name]

def col_def(column)
  case column["type"]
  when "number"
    'r'
  when "string"
    'l'
  end
end

MAX_CODE_LENGTH = 50
DAYS = %w(Sunday Monday Tuesday Thursday Friday Saturday)

def col_data(column, value)
  code = value
  if column["truncate"]
    slices = value.chars.each_slice(32).take(4).map(&:join).map { |slice|
      '\small\texttt{'.latex! + slice.to_latex + '}'.latex!
    }
    code = '\pbox{20cm}{'.latex! + slices.join(' \\\\ ').latex! + '...}'.latex!
    return code
  end
  code = code.to_latex
  if column["day"]
    code << (' \small{(' + DAYS[value.to_i] + ')}').latex!
  end
  if column["time"]
    v = value.to_i
    code << (' \small{(' + ('%02d:%02d' % v.divmod(60)) + ')}').latex!
  end
  code = "\\texttt{#{code}}" if column["code"] || value == "NULL"
  code = "\\emph{#{code}}" if value == "NULL"
  code
end

out = []
out << '\begin{tabular}{' << style.map { |column| col_def(column) }.join << '}'
if styles['headers'].include?(name)
  out << '\multicolumn{' + style.length.to_s + '}{c}{\textbf{Table \texttt{' + name + '}}} \\\\'
end
out << '\hline \NR'

out << style.map { |column| '\texttt{' + column["column"].to_latex + '}' }.join(' & ')

out << '\\\\ \NR \hline \NR'
data.each do |row|
  out << style.map { |column|
    value = row[column["column"].to_sym]
    col_data(column, value || '')
  }.join(' & ') + ' \\\\ \NR'
end

out << '\hline'

out << '\end{tabular}'

File.write(output, out.join("\n"))






