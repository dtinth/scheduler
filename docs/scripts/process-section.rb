
require 'ostruct'

class Processor

  def initialize
    @types = []
  end

  def type(pattern, &block)
    @types << OpenStruct.new(pattern: pattern, block: block)
  end

  def run!
    input = ARGV[0]
    output = ARGV[1]
    content = File.read(input)
    puts "#{input} => #{output}"
    @types.each do |type|
      if File.extname(input) == type.pattern
        type.block[content, input, output]
        return
      end
    end
    system "cp #{input} #{output}"
  end

end

p = Processor.new

p.type '.md' do |content, input, output|
  system "pandoc -f markdown -t latex < #{input} > #{output}"
end

p.run!




