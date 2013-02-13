all: index.xml

index.tmp:
	ls -t *-*-*-*.xml > index.tmp

index.xml: index.tmp
	./genindex

clean:
	rm -f index.xml