
require 'redcarpet'

markdown = Redcarpet::Markdown.new(Redcarpet::Render::HTML, :fenced_code_blocks => true, :tables => true, :no_intra_emphasis => true)
puts markdown.render($stdin.read)
