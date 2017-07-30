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
@app.route('/<category>/')
def index(category=None):
    page = int(request.args.get('page', 0))
    return render_template('index.html', posts=getPosts(page, category), next_page=page+1)


@app.route('/<category>/<slug>')
def showPost(category, slug):
    try:
        post = getPost(category, slug)
        return render_template('index.html', posts=[post])
    except IOError:
        return not_found(None)


def getPost(category, slug):
    file = 'posts/{cat}/{slug}.html'.format(cat=category, slug=slug)
    if not os.path.isfile(file):
        raise IOError
    return {
        'category': category,
        'slug': slug,
        'updated': datetime.fromtimestamp(os.path.getmtime(file)),
    }


def getPosts(page, request_category=None):
    page_size = 10
    posts = []

    for category, _, files in list(os.walk('posts/'))[1:]:
        category = category.replace('posts/', '')
        if request_category is None or request_category == category:
            for name in files:
                if name[0] != '.':
                    posts.append(getPost(category, name.replace('.html', '')))

    posts = sorted(posts, key=lambda post: post['updated'], reverse=True)

    posts_from = page_size * page
    posts_to = posts_from + page_size

    return posts[posts_from:posts_to]


@app.errorhandler(404)
def not_found(error):
    return render_template('404.html'), 404


if __name__ == '__main__':
    app.run(host='0.0.0.0', debug=True)
