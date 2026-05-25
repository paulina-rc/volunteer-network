from app import create_app
from app.models.user import Usuario


app = create_app()


with app.app_context():

    usuarios = Usuario.query.all()

    print(usuarios)



if __name__ == "__main__":

    app.run(debug=True)