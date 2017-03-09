from datetime import datetime
import os

from flask import Flask, render_template, request
from jinja2 import ChoiceLoader, FileSystemLoader


app = Flask(__name__)

app.jinja_loader = ChoiceLoader([
    app.jinja_loader,
    FileSystemLoader(['templates', 'posts']),
])


@app.route('/')
def index():
    page = int(request.args.get('page', 0))
    return render_template('index.html', posts=getPosts(page), next_page=page+1)


@app.route('/<slug>')
def showPost(slug=None):
    try:
        post = getPost(slug)
        return render_template('index.html', posts=[post])
    except IOError:
        return not_found('Could not find ' + slug)


def getPost(slug):
    file = 'posts/' + slug + '.html'
    if not os.path.isfile(file):
        raise IOError
    return {
        'slug': slug,
        'updated': datetime.fromtimestamp(os.path.getmtime(file)),
    }


def getPosts(page):
    page_size = 10

    files = os.listdir('posts/')
    posts = []
    for file in files:
        if file[0] != '.':
            posts.append(getPost(file.replace('.html', '')))
    posts = sorted(posts, key=lambda post: post['updated'], reverse=True)

    posts_from = page_size * page
    posts_to = posts_from + page_size
    return posts[posts_from:posts_to]


@app.errorhandler(404)
def not_found(error):
    return render_template('404.html'), 404


if __name__ == '__main__':
    app.run(host='0.0.0.0', debug=True)
