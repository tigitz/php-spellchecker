
FROM registry.fedoraproject.org/fedora:latest
ARG VERSION=master

ENV SOURCE_URL=https://github.com/bakwc/JamSpell/archive/${VERSION}.tar.gz
ENV SOURCE_DIR=/opt/source

RUN dnf -y install gcc cmake3 gcc-c++ make tar curl libstdc++-devel \
    && dnf -y clean all \
    && install -d ${SOURCE_DIR} \
    && curl -Ls ${SOURCE_URL} | tar -xvz --strip-components=1 -C ${SOURCE_DIR}

WORKDIR ${SOURCE_DIR}

RUN mkdir build \
    && cd build \
    && cmake3 .. \
    && make


FROM registry.fedoraproject.org/fedora:latest
ARG VERSION=master

ENV HOST=0.0.0.0
ENV PORT=8080
ENV DATA_DIR=/opt/jamspell/data
ENV MODEL=en.bin
ENV MODEL_SOURCE_URL=https://github.com/bakwc/JamSpell-models/raw/${VERSION}/en.tar.gz

RUN dnf -y install langpacks-en libstdc++ \
    && dnf -y clean all \
    && install -d ${DATA_DIR} \
    && curl -Ls ${MODEL_SOURCE_URL} | tar -xvz -C ${DATA_DIR}


COPY --from=0 /opt/source/build/web_server/web_server /usr/bin/jamspell-server
COPY ./assets/entrypoint.sh /usr/bin/entrypoint

VOLUME ["/opt/jamspell/data"]

EXPOSE 8080/tcp

ENTRYPOINT /usr/bin/entrypoint

