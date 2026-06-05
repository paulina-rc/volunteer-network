from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from config import Config

db = SQLAlchemy()


def create_app():

    app = Flask(__name__)

    app.config.from_object(Config)

    db.init_app(app)

    with app.app_context():

        from app.models.user import Usuario
        from app.models.voluntariado import Voluntariado
        from app.models.postulacion import Postulacion
        from app.models.certificacion import Certificacion

        db.create_all()

    return app