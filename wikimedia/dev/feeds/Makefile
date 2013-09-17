FEED_USER=surfboard

all: permissions usersfeed

permissions:
	chmod 755 user project
	chown $(FEED_USER) user project
	cd user ; chmod +x genindex ; chown $(FEED_USER) *.xml
	cd project ; chown $(FEED_USER) *.xml

clean:
	cd user ; make clean

usersfeed:
	cd user ; make
