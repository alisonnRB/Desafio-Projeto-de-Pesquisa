FROM alpine:latest

ENV DEBIAN_FRONTEND=noninteractive

RUN apk add --no-cache wget unzip curl bash && \
    wget https://releases.hashicorp.com/terraform/1.1.4/terraform_1.1.4_linux_amd64.zip -O /tmp/terraform.zip && \
    unzip /tmp/terraform.zip -d /usr/bin && \
    rm /tmp/terraform.zip

WORKDIR /workspace

COPY init_terraform.sh /usr/local/bin/init_terraform.sh
RUN chmod +x /usr/local/bin/init_terraform.sh

COPY . /workspace

ENTRYPOINT ["/usr/local/bin/init_terraform.sh"]
