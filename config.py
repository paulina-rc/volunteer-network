class Config:

    SECRET_KEY='clave'

    SQLALCHEMY_DATABASE_URI = (
       'mysql+pymysql://root:@127.0.0.1/red_voluntariados'
    )

    SQLALCHEMY_TRACK_MODIFICATIONS=False